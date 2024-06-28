<?php

namespace App\DB\Repositories;

use Jajo\JSONDB;

class CustomerRepository
{
	const DB_FILE = "customers.json";

	public function __construct(
		private JSONDB $db,
	)
	{
	}

	public function getAll() : array
	{
		return $this->db->select( ['id', 'name'] )
			->from(self::DB_FILE)
			->get();
	}

	public function get(int $customerId)
	{
		$result = $this->db->select( '*' )
			->from(self::DB_FILE)
			->where( ['id' => $customerId ])
			->get();

		if (count($result) == 1) {
			return $result[0];
		}
		return $result;
	}
}
