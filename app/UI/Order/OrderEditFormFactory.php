<?php

declare(strict_types=1);

namespace App\UI\Order;

use App\DB\Entities\Contract;
use App\DB\Entities\Customer;
use App\DB\Entities\Order;
use App\DB\Entities\Status;
use App\DB\Enums\StatusEnum;
use App\DB\Facades\ORMEntityFacade;
use App\DB\Repositories\OrderRepository;
use App\DB\Utils\DateTimeConverter;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SelectBox;
use Nette\Utils\DateTime;

/**
 * @phpstan-import-type OrderArray from Order
 */
class OrderEditFormFactory
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly ORMEntityFacade $ORMEntityFacade,
    ) {
    }

    public function create(int $orderId, string $linkChangeData): Form
    {
        $order = $this->ORMEntityFacade->getOrder($orderId);
        $form = new Form();
        $form->addProtection();
        $form->addHidden("id", $orderId);

        $form->addSubmit('save', 'Save');

        $form->addGroup('left');

        $form->addText('orderNumber', 'order number')
            ->addRule(Form::MaxLength, 'Maximalni delka je %d znaku', 20)
            ->setRequired();

        $form->addDateTime('requestedDeliveryAt', 'Delivery at', withSeconds: true)
            ->setFormat("Y-m-d H:i:s")
            ->setRequired();

        $customersForSelect = $this->prepareForSelect($this->ORMEntityFacade->getCustomers());
        $customer = $form->addSelect('customer', 'Customer', $customersForSelect)
            ->setPrompt('Select customer')
            ->addRule(Form::Filled)
            ->setRequired();

        $contractsForSelect = $this->loadContracts($order->getCustomer()->getId());
        $contract = $form->addSelect('contract', 'Contract', $contractsForSelect)
            ->setPrompt('Select contract')
            ->addRule(Form::Filled)
            ->setHtmlAttribute('data-depends', $customer->getHtmlName())
            ->setHtmlAttribute('data-url', $linkChangeData)
            ->setRequired();

        $form->addGroup('right');
        $statuses = array_merge(...array_map(fn ($item) => [
            $item->value => $item->name,
        ], StatusEnum::cases()));
        $form->addSelect('status', 'Status', $statuses);

        $form->onAnchor[] = function (Form $form): void {
            /** @var SelectBox $customer */
            $customer = $form['customer'];
            /** @var SelectBox $contract */
            $contract = $form['contract'];
            $customerId = $customer->getValue();

            if ($customerId === null) {
                $contract->setItems([]);
                return;
            }

            if (! is_int($customerId)) {
                throw new \LogicException("CustomerID should be int");
            }

            $contract->setItems($this->loadContracts($customerId));
        };

        $form->onSuccess[] = function ($form): void {
            $this->processOrderEditForm($form);
        };

        $form->onSubmit[] = function ($form) {
            $presenter = $form->getPresenter();
            if ($presenter && $form->getPresenter()->isAjax()) {
                $form->getPresenter()->redrawControl('orderEditFormSniper');
            }
        };

        $form->setDefaults($order->toArray());

        return $form;
    }

    /**
     * @param int $customerId
     * @return array<int|string, string>
     */
    public function loadContracts(int $customerId): array
    {
        return $this->prepareForSelect($this->ORMEntityFacade->getContractsByCustomerId($customerId));
    }

    /**
     * @param Form $form
     * @return void
     */
    public function processOrderEditForm(Form $form): void
    {
        /** @var array<string, mixed> $data */
        $data = $form->getValues();
        $data['createdAt'] = DateTimeConverter::createNow();
        $data['requestedDeliveryAt'] = DateTime::createFromFormat("Y-m-d H:i:s", $data['requestedDeliveryAt'], new \DateTimeZone('Europe/Prague'));

        /** @var OrderArray $data */
        $this->orderRepository->save($this->ORMEntityFacade->createOrder($data));

        $presenter = $form->getPresenter();
        if ($presenter && $presenter->isAjax()) {
            $presenter->redrawControl('orderEditFormSniper');
        }
    }

    /**
     * @param array<Customer|Contract|Status> $data
     * @return array<int|string, string>
     */
    private function prepareForSelect(array $data): array
    {
        // return array_merge(... array_map(fn ($item) => ["{$item->getId()}" => $item->getName()], $data));
        // ^ this do not work due to integer keys
        $forSelect = [];
        foreach ($data as $item) {
            $forSelect[$item->getId()] = $item->getName();
        }
        return $forSelect;

    }
}
