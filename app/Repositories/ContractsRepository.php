<?php

namespace App\Repositories;

class ContractsRepository
{

	public function getAll()
	{
		return [
			21 => "customer-sale",
			321 => "customer-sale",
			12 => "partner-sale",
		];
	}

	public function get($customerId)
	{
		switch ($customerId) {
			case 23:
				return [21 => "customer-sale"];
			case 409:
				return [321 => "customer-sale"];
			case 143:
				return [12 => "partner-sale"];
		}
	}
}
