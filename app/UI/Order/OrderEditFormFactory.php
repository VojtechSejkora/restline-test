<?php

namespace App\UI\Order;

use App\Entities\Contract;
use App\Entities\Order;
use App\Enums\StatusEnum;
use App\Repositories\ContractsRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\OrderRepository;
use Nette\Application\UI\Form;
use Tracy\Debugger;

class OrderEditFormFactory
{

	public function __construct(
		private readonly CustomerRepository $customerRepository,
		private readonly ContractsRepository $contractsRepository,
		private readonly OrderRepository $orderRepository,
	)
	{
	}

	public function create(int $orderId) : Form
	{
		$form = new Form();
		$form->addProtection();
		$form->addHidden("id", $orderId);

		$form->addSubmit('save', 'Save');

		$form->addGroup('left');

		$form->addText( 'orderNumber','order number')
			->addRule(Form::MaxLength, 'Maximalni delka je %d znaku', 20)
			->setRequired();


		$form->addDateTime( 'deliveryAt','Delivery at')
			->setRequired();

		$customer = $form->addSelect( 'customer','Customer', $this->customerRepository->getAll())
			->setPrompt('Select customer')
			->addRule(Form::Filled)
			->setRequired();

		$contract = $form->addSelect( 'contract','Contract')
			->setPrompt('Select contract')
			->addRule(Form::Filled)
			->setHtmlAttribute('data-depends', $customer->getHtmlName())
			->setRequired();

		$form->addGroup('right');
		$statuses = array_merge(... array_map(fn($item) => [$item->value => $item->name], StatusEnum::cases()));
		$form->addSelect('status', 'Status', $statuses);

		$form->onAnchor[] = fn() =>
			$contract->setItems($customer->getValue()
				? $this->contractsRepository->get($customer->getValue())
				: []);

		$form->onSuccess[] = [$this, 'processOrderEditForm'];

		$form->setDefaults($this->orderRepository->get($orderId)->toArray());

		return $form;
	}

	public function handleLoadContract($customerId) {
		return $this->contractsRepository->get($customerId);
	}

	public function processOrderEditForm(Form $form, array $data)
	{

		$this->orderRepository->save($data);

	}
}
