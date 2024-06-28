<?php

namespace App\UI\Order;

use App\Repository\OrderRepository;
use Tracy\Debugger;
use Ublaboo\DataGrid\DataGrid;


class OrderDataGridFactory
{
	/**
	 * @var DataGrid
	 */
	private $grid;
	public function __construct(
		private readonly OrderRepository $orderRepository,
	)
	{
		Debugger::barDump($orderRepository);
		$this->grid = new DataGrid();
	}
	public function create() : DataGrid
	{
		$grid = $this->grid;

		Debugger::barDump($grid->getDataSource());

		$grid->addColumnNumber('id', 'id')
			->setSortable()
			->setSortableResetPagination();
		$grid->addColumnNumber('orderNumber', 'orderNumber')
			->setSortable()
			->setSortableResetPagination();
		$grid->addColumnDateTime('createdAt', 'createdAt')
			->setSortable()
			->setSortableResetPagination();
		$grid->addColumnDateTime('deliveryAt', 'DeliveryAt', "requestedDeliveryAt")
			->setSortable()
			->setSortableResetPagination();
		$grid->addColumnText('customer', 'Customer', 'customer.name')
			->setFilterText();
		$grid->addColumnText("contract",'Contract', 'contract.name' )
			->setFilterText();
		$grid->addColumnStatus('status', 'Status', 'status.id')
			->addOption("ACT", 'Active')
			->setClass('btn btn-success')
			->setIcon( 'check')
			->endOption()
			->addOption(  "END", 'Close')
			->setClass('btn btn-danger')
			->setIcon( 'close')
			->endOption()
			->addOption(  "NEW", 'New')
			->setClass('btn btn-primary')
			->endOption()
			->onChange[] = [$this, 'processStatusChange'];

		$grid->addAction('edit',  "edit")
			->setIcon('pencil');

		$grid->setDefaultSort(['id' => 'DESC']);
		return $grid;
	}

	public function processStatusChange($id, $newStatus): void
	{
		$this->orderRepository->changeStatus($id, $newStatus);
		$this->grid->setDataSource($this->orderRepository->loadData());
		if ($this->isAjax()) {
			$this['columnsGrid']->redrawItem($id);
		}
	}
}
