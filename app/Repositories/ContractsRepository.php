<?php

namespace App\Repositories;

use Jajo\JSONDB;

class ContractsRepository
{

	const DB_FILE = 'contracts.json';

	public function __construct(
		private JSONDB $db,
	)
	{
	}

	public function getAll()
	{
		return $this->db->select('*')
			->from( self::DB_FILE )
			->get();
	}

	public function getByCustomer($customerId)
	{
		return $this->db->select( '*' )
			->from( self::DB_FILE )
			->where( ['customer' => $customerId] )
			->get();
	}

	public function get($contractId)
	{
		return $this->db->select( '*' )
			->from( self::DB_FILE )
			->where( ['id' => $contractId] )
			->get();

	}
}
