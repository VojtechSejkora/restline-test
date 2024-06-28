<?php

namespace App\UI\Order;

use App\UI\common\BasePresenter;
use Nette;
use Tracy\Debugger;

class OrderPresenter extends BasePresenter
{
	public function __construct(
		private readonly OrderDataGridFactory $orderDataGridFactory,
	)
	{
		Debugger::barDump($orderDataGridFactory);
	}

	public function createComponentOrderDataGrid(): ?Nette\ComponentModel\IComponent
	{
		return $this->orderDataGridFactory->create();
	}
}
