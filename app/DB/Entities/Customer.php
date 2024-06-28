<?php

namespace App\DB\Entities;

class Customer
{
	public function __construct(
		private int $id,
		private string $name,
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

	public function toArray()
	{
		return [
			'id' => $this->getId(),
			'name' => $this->getName(),
		];
	}
}
