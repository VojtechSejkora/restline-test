<?php

namespace App\UI\Order;

use App\DB\Facades\ORMEntityFacade;
use App\DB\Repositories\OrderRepository;
use Error;
use Nette\Utils\Strings;
use Tracy\Debugger;
use Ublaboo\DataGrid\DataGrid;

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
			->setSortableCallback($this->sortCallback())
			->setSortableResetPagination();
		$grid->addColumnNumber('orderNumber', 'orderNumber')
			->setSortable()
			->setSortableCallback($this->sortCallback())
			->setSortableResetPagination();
		$grid->addColumnDateTime('createdAt', 'createdAt')
			->setSortable()
			->setSortableCallback($this->sortCallback())
			->setSortableResetPagination();
		$grid->addColumnDateTime('requestedDeliveryAt', 'deliveryAt', )
			->setSortable()
			->setSortableCallback($this->sortCallback())
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
		$newStatus = $this->ORMEntityFacade->createStatus($newStatus);
		$this->orderRepository->changeStatus($id, $newStatus);
		$this->grid->setDataSource($this->getDataSource());
		if ($this->grid->getPresenter()->isAjax()) {
			$this->grid->getPresenter()['columnsGrid']->redrawItem($id);
		}
	}

	private function getDataSource()
	{
		return $this->ORMEntityFacade->getOrders();
	}

	private function get($obj, $column)
	{
		foreach (Strings::split($column, '[\.]') as $selector) {
			$getMethod = 'get' . ucfirst($selector);
			try {
				$obj = $obj->$getMethod();
			}catch (Error $e) {
				Debugger::barDump($obj);
				throw $e;
			}
		}

		return $obj;
	}

	public function sortCallback() : \Closure
	{
		return function ($datasource, $sortType) {
			usort($datasource, $this->sort($sortType));
			return $datasource;
		};
	}
	public function sort($keyOrder) : \Closure
	{
		foreach ($keyOrder as $key => $order) {
			// potentially improve to allow multiple sorting columns
			return function ($objA, $objB) use ($key, $order) {
				$a = $this->get($objA, $key);
				$b = $this->get($objB, $key);

				if ($a == $b) {
					return 0;
				}

				return (($a < $b) ? -1 : 1) * (($order == 'ASC') ? 1 : -1);
			};
		}
		return function ($a, $b) {};
	}
}
