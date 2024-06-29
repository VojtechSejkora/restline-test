<?php

namespace App\DB\Repositories;
use App\DB\Entities\Order;
use App\DB\Entities\Status;
use Jajo\JSONDB;
use Tracy\Debugger;
use Tracy\ILogger;

class OrderRepository
{
	const DB_FILE = 'orders.json';

	public function __construct(
		private JSONDB $db,
	)
	{
	}

	public function getAll()
	{
		return $this->loadData();
	}

	public function loadData() : array
	{
		return $this->db->select( '*' )
			->from( self::DB_FILE )
			->get();
	}

	public function changeStatus(int $orderId, Status $newStatus) : void
	{
		$order = $this->get($orderId);

		if ($order['status']['id'] == $newStatus->getId()) {
			return;
		}

		$order['status'] = $newStatus->toArray();

		$this->_save($order);
	}

	public function save(Order $order)
	{
		$this->_save($order->toArray(true));
	}

	public function _save(array $order)
	{
		$jsonData = json_encode($order);
		Debugger::log("Storing id {$order['id']} data {$jsonData}");
		$this->db->update($order)
			->from( self::DB_FILE )
			->where( [ 'id' => $order['id'] ] )
			->trigger();
	}

	public function get(int $orderId)
	{
		$result = $this->db->select('*')
			->from(self::DB_FILE)
			->where(['id' => $orderId])
			->get();

		if (count($result) == 1) {
			return $result[0];
		}
		return $result;
	}

	private static function flatten(array $data) : array
	{
		$change = True;
		while ($change) {
			$change = False;
			foreach ($data as $key => $value) {
				if (!is_array( $value)) {
					continue;
				}
				foreach ($value as $k => $v) {
					$data["{$key}.{$k}"] = $v;
				}
				unset($data[$key]);
				$change = True;
			}
		}
		return $data;
	}
}