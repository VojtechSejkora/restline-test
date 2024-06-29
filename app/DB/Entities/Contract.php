<?php

namespace App\DB\Entities;

/**
 * @phpstan-type ContractArray array{id: int, name: string, customer:int}
 */
class Contract
{

	public function __construct(
		private int $id,
		private string $name,
		private Customer $customer,
	)
	{

	}

	public function getId(): int
	{
		return $this->id;
	}

	public function setId(int $id): void
	{
		$this->id = $id;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}


	public function getCustomer(): Customer
	{
		return $this->customer;
	}

	public function setCustomer(Customer $customer): void
	{
		$this->customer = $customer;
	}

	/**
	 * @return ContractArray
	 */
	public function toArray() : array
	{
		return [
			'id' => $this->getId(),
			'name' => $this->getName(),
			'customer' => $this->getCustomer()->getId(),
		];
	}
}
