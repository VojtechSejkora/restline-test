<?php

declare(strict_types=1);

namespace App\UI\Order;

use App\DB\Entities\Order;
use App\DB\Facades\ORMEntityFacade;
use App\DB\Repositories\OrderRepository;
use App\DB\Utils\DataSource;
use App\DB\Utils\DateTimeConverter;
use Nette\Utils\DateTime;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\DataSource\IDataSource;
use Ublaboo\DataGrid\Exception\DataGridColumnStatusException;
use Ublaboo\DataGrid\Exception\DataGridException;

/**
 * @phpstan-import-type OrderArray from Order
 */
class OrderDataGridFactory
{
    /**
     * @var DataGrid
     */
    private $grid;

    public function __construct(
        private readonly ORMEntityFacade $ORMEntityFacade,
        private readonly OrderRepository $orderRepository,
    ) {
        $this->grid = new DataGrid();
    }

    public function create(): DataGrid
    {
        $grid = $this->grid;

        try {

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
            $grid->addColumnText("contract", 'Contract', 'contract.name')
                ->setFilterText();

            $grid->addColumnStatus('status', 'Status', 'status.id')
                ->addOption("ACT", 'Active')
                ->setClass('btn btn-success')
                ->setIcon('check')
                ->endOption()
                ->addOption("END", 'Close')
                ->setClass('btn btn-danger')
                ->setIcon('close')
                ->endOption()
                ->addOption("NEW", 'New')
                ->setClass('btn btn-primary')
                ->setClassInDropdown('hidden disabled')
                ->endOption()
                ->onChange[] = [$this, 'processStatusChange'];

            $grid->addAction('edit', "edit")
                ->setIcon('pencil');

            $grid->setDefaultSort([
                'id' => 'DESC',
            ]);
        } catch (DataGridColumnStatusException|DataGridException $e) {
            throw new \LogicException("Invalid logic creating grid", previous: $e);
        }

        return $grid;
    }

    public function processStatusChange(int $id, string $newStatus): void
    {
        $newStatus = $this->ORMEntityFacade->createStatus($newStatus);
        $this->orderRepository->changeStatus($id, $newStatus);
        $this->grid->setDataSource($this->getDataSource());
        $presenter = $this->grid->getPresenter();
        if ($presenter?->isAjax()) {
            $this->grid->redrawItem($id);
        }
    }

    private function getDataSource(): IDataSource
    {
        return new DataSource($this->ORMEntityFacade->getOrders());
    }

    private function timeRender(string $column): \Closure
    {
        return function ($row) use ($column): string {
            $time = DataSource::get($row, $column);
            if ($time instanceof DateTime || is_null($time)) {
                return DateTimeConverter::format($time) ?? "";
            }
            if (is_string($time)) {
                return $time;
            }
            return "";
        };
    }
}
