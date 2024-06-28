<?php

namespace App\Repositories;
use App\Entities\Order;
use App\Entities\Status;
use Jajo\JSONDB;
use Tracy\Debugger;

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
		$data = [];
		$orders = $this->db->select( '*' )
			->from( self::DB_FILE )
			->get();

		Debugger::barDump($orders);
		foreach ($orders as $item) {
			$data[] = $this->flatten($item);
		}
		return $data;
	}

	public function changeStatus(int $orderId, Status $newStatus) : void
	{
		$order = $this->db->select( '*' )
			->from( self::DB_FILE )
			->where([ 'id' => $orderId ] )
			->get();


		if ($order['status']['id'] == $newStatus->getId()) {
			return;
		}

		$order['status'] = $newStatus->toArray();

		$this->db->update( $order )
			->from( self::DB_FILE )
			->where( [ 'id' => $orderId ] )
			->trigger();
	}

	public function save(Order $order)
	{
		$this->db->update( $order->toArray(true) )
			->from( self::DB_FILE )
			->where( [ 'id' => $order->getId() ] )
			->trigger();
	}

	public function get(int $orderId)
	{
		return $this->db->select('*')
			->from(self::DB_FILE)
			->where(['id' => $orderId])
			->get();
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
