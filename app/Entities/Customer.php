<?php

namespace App\Entities;

class Customer
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

	public function setCustomer(int $customer): void
	{
		$this->customer = $customer;
	}
}
