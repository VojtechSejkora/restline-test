<?php

declare(strict_types=1);

namespace App\DB\Repositories;

use App\DB\Entities\Order;
use App\DB\Entities\Status;
use Jajo\JSONDB;
use Tracy\Debugger;

/**
 * @phpstan-import-type OrderArray from Order
 */
class OrderRepository
{
    public const string DB_FILE = 'orders.json';

    public function __construct(
        private readonly JSONDB $db,
    ) {
    }

    /**
     * @return array<OrderArray>
     */
    public function getAll(): array
    {
        return $this->loadData();
    }

    /**
     * @return array<OrderArray>
     */
    public function loadData(): array
    {
        return $this->db->select('*')
            ->from(self::DB_FILE)
            ->get();
    }

    public function changeStatus(int $orderId, Status $newStatus): void
    {
        $order = $this->get($orderId);

        if (is_array($order['status'])) {
            $oldStatusId = $order['status']['id'];
        } else {
            $oldStatusId = $order['status'];
        }

        if ($oldStatusId == $newStatus->getId()) {
            return;
        }

        $order['status'] = $newStatus->toArray();

        $this->forceSave($order);
    }

    public function save(Order $order): void
    {
        $this->forceSave($order->toArray(true));
    }

    /**
     * @param OrderArray $order
     * @return void
     */
    public function forceSave(array $order): void
    {
        $jsonData = json_encode($order);
        Debugger::log("Storing id {$order['id']} data {$jsonData}");
        $this->db->update($order)
            ->from(self::DB_FILE)
            ->where([
                'id' => $order['id'],
            ])
            ->trigger();
    }

    /**
     * @param int $orderId
     * @return OrderArray
     */
    public function get(int $orderId): array
    {
        $result = $this->db->select('*')
            ->from(self::DB_FILE)
            ->where([
                'id' => $orderId,
            ])
            ->get();

        if (count($result) === 1) {
            return $result[0];
        }
        return $result;
    }
}
