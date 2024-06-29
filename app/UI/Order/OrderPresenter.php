<?php

namespace App\UI\Order;

use App\DB\Facades\ORMEntityFacade;
use App\UI\common\BasePresenter;
use Nette;
use Tracy\Debugger;

class OrderPresenter extends BasePresenter
{

	public function __construct(
		private readonly OrderDataGridFactory $orderDataGridFactory,
		private readonly OrderEditFormFactory $orderEditFormFactory,
		private readonly ORMEntityFacade $ORMEntityFacade,
	)
	{
	}

	public function actionEdit($id)
	{
		$this->template->id = $id;
	}
	public function createComponentOrderDataGrid(): ?Nette\ComponentModel\IComponent
	{
		return $this->orderDataGridFactory->create();
	}

	public function createComponentOrderEditForm(): ?Nette\ComponentModel\IComponent
	{
		$orderId = $this->template->id;
		$form = $this->orderEditFormFactory->create($orderId);
		$form->getComponent('contract')
			->setHtmlAttribute('data-url', $this->link('loadContract!', '#'));


		return $form;
	}

	public function handleLoadContract($customerId)
	{
		$this->sendJson($this->orderEditFormFactory->loadContracts($customerId));
	}
}
