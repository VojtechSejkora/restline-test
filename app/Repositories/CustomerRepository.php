<?php

namespace App\Repositories;

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
		return $this->db->select( '*' )
			->from(self::DB_FILE)
			->where( ['id' => $customerId ])
			->get();

	}
}
