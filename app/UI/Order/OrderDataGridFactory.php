<?php

namespace App\UI\Order;

use App\DB\Utils\DataSource;
use App\DB\Facades\ORMEntityFacade;
use App\DB\Repositories\OrderRepository;
use App\DB\Utils\DateTimeConverter;
use Error;
use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use Tracy\Debugger;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\DataSource\IDataSource;

class OrderDataGridFactory
{
	/**
	 * @var DataGrid
	 */
	private $grid;
	public function __construct(
		private readonly ORMEntityFacade $ORMEntityFacade,
		private readonly OrderRepository $orderRepository,
	)
	{
		$this->grid = new DataGrid();
	}
	public function create() : DataGrid
	{
		$grid = $this->grid;


		$this->grid->setDataSource($this->getDataSource());

		$grid->addColumnNumber('id', 'id')
			->setSortable()
			->setSortableResetPagination();
		$grid->addColumnNumber('orderNumber', 'orderNumber')
			->setSortable()
			->setSortableResetPagination();
		$grid->addColumnDateTime('createdAt', 'createdAt')
			->setRenderer($this->timeRender('createdAt'))
			->setSortable()
			->setSortableResetPagination();
		$grid->addColumnDateTime('requestedDeliveryAt', 'deliveryAt')
			->setRenderer($this->timeRender('requestedDeliveryAt'))
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
				->setClassInDropdown('hidden disabled')
			->endOption()
			->onChange[] = [$this, 'processStatusChange'];

		$grid->addAction('edit',  "edit")
			->setIcon('pencil');

		$grid->setDefaultSort(['id' => 'DESC']);
		return $grid;
	}

	public function processStatusChange($id, $newStatus): void
	{
		$newStatus = $this->ORMEntityFacade->createStatus($newStatus);
		$this->orderRepository->changeStatus($id, $newStatus);
		$this->grid->setDataSource($this->getDataSource());
		if ($this->grid->getPresenter()->isAjax()) {
			$this->grid->redrawItem($id);
		}
	}

	private function getDataSource() : IDataSource
	{
		return new DataSource($this->ORMEntityFacade->getOrders());
	}

	private function timeRender(string $column) : \Closure
	{
		return fn ($datetime) => DateTimeConverter::format(DataSource::get($datetime, $column));
	}
}
