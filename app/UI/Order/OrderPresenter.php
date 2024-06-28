<?php

namespace App\UI\Order;

use App\UI\common\BasePresenter;
use Nette;
use Tracy\Debugger;

class OrderPresenter extends BasePresenter
{
	public function __construct(
		private readonly OrderDataGridFactory $orderDataGridFactory,
		private readonly OrderEditFormFactory $orderEditFormFactory,
	)
	{
		Debugger::barDump($orderDataGridFactory);
	}

	public function createComponentOrderDataGrid(): ?Nette\ComponentModel\IComponent
	{
		return $this->orderDataGridFactory->create();
	}

	public function createComponentOrderEditForm(): ?Nette\ComponentModel\IComponent
	{

		$form = $this->orderEditFormFactory->create();
		$form->getComponent('contract')
			->setHtmlAttribute('data-url', $this->link('loadContract!', '#'));
		return $form;
	}

	public function handleLoadContract($customerId)
	{
		$this->sendJson($this->orderEditFormFactory->handleLoadContract($customerId));
	}
}
