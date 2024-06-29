<?php

namespace App\DB\Repositories;

use App\DB\Entities\Contract;
use App\DB\Entities\Customer;
use App\DB\Entities\Order;
use App\DB\Entities\Status;
use Jajo\JSONDB;

/**
 * @phpstan-import-type ContractArray from Contract
 */
class ContractsRepository
{

	const DB_FILE = 'contracts.json';

	public function __construct(
		private JSONDB $db,
	)
	{
	}

	/**
	 * @return array<ContractArray>
	 */
	public function getAll() : array
	{
		return $this->db->select('*')
			->from( self::DB_FILE )
			->get();
	}

	/**
	 * @return array<ContractArray>
	 */
	public function getByCustomer(int $customerId) : array
	{
		return $this->db->select( '*' )
			->from( self::DB_FILE )
			->where( ['customer' => $customerId] )
			->get();
	}

	/**
	 * @param int $contractId
	 * @return array{}|ContractArray
	 */
	public function get(int $contractId)
	{
		$result = $this->db->select( '*' )
			->from( self::DB_FILE )
			->where( ['id' => $contractId] )
			->get();


		if (count($result) >= 1) {
			return $result[0];
		} else {
			return [];
		}

	}
}
