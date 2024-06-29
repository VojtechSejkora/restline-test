<?php

namespace App\UI\Order;

use App\DB\Enums\StatusEnum;
use App\DB\Facades\ORMEntityFacade;
use App\DB\Repositories\ContractsRepository;
use App\DB\Repositories\CustomerRepository;
use App\DB\Repositories\OrderRepository;
use App\DB\Utils\DateTimeConverter;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\DateTimeControl;
use Nette\Utils\DateTime;
use Tracy\Debugger;

class OrderEditFormFactory
{

	public function __construct(
		private readonly OrderRepository $orderRepository,
		private readonly ORMEntityFacade $ORMEntityFacade,
	)
	{
	}

	public function create(int $orderId) : Form
	{
		$order = $this->ORMEntityFacade->getOrder($orderId);
		$form = new Form();
		$form->addProtection();
		$form->addHidden("id", $orderId);

		$form->addSubmit('save', 'Save');

		$form->addGroup('left');

		$form->addText( 'orderNumber','order number')
			->addRule(Form::MaxLength, 'Maximalni delka je %d znaku', 20)
			->setRequired();


		$form->addDateTime( 'requestedDeliveryAt','Delivery at', withSeconds: True)
			->setFormat("Y-m-d H:i:s")
			->setRequired();

		$customersForSelect = $this->prepareForSelect($this->ORMEntityFacade->getCustomers());
		$customer = $form->addSelect( 'customer','Customer', $customersForSelect)
			->setPrompt('Select customer')
			->addRule(Form::Filled)
			->setRequired();

		$contractsForSelect =  $this->loadContracts($order->getCustomer()->getId());
		$contract = $form->addSelect( 'contract','Contract', $contractsForSelect)
			->setPrompt('Select contract')
			->addRule(Form::Filled)
			->setHtmlAttribute('data-depends', $customer->getHtmlName())
			->setRequired();

		$form->addGroup('right');
		$statuses = array_merge(... array_map(fn($item) => [$item->value => $item->name], StatusEnum::cases()));
		$form->addSelect('status', 'Status', $statuses);

		$form->onAnchor[] = fn() =>
			$contract->setItems($customer->getValue()
				? $this->loadContracts($customer->getValue())
				: []);

		$form->onSuccess[] = [$this, 'processOrderEditForm'];
		$form->onSubmit[] = function ($form) {
			if ($form->getPresenter()->isAjax()) {
				$form->getPresenter()->redrawControl('orderEditFormSniper');
			}
		};

		$form->setDefaults($order->toArray());


		return $form;
	}

	public function loadContracts($customerId) {
		return $this->prepareForSelect($this->ORMEntityFacade->getContractsByCustomerId($customerId));
	}

	public function processOrderEditForm(Form $form, array $data)
	{
		$data['createdAt'] = DateTimeConverter::createNow();
		$data['requestedDeliveryAt'] = DateTime::createFromFormat("Y-m-d H:i:s", $data['requestedDeliveryAt'], new \DateTimeZone('Europe/Prague'));

		$this->orderRepository->save($this->ORMEntityFacade->createOrder($data));

		if ($form->getPresenter()->isAjax()) {
			$form->getPresenter()->redrawControl('orderEditFormSniper');
		} else {
			$form->getPresenter()->redirect('this');
		}
	}

	private function prepareForSelect(array $data)
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
