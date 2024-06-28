<?php

namespace App\Entities;

use DateTimeZone;
use Nette\Database\DateTime;

class Order
{
	public function __construct(
		private int $id,
		private string $orderNumber,
		private DateTime $createdAt,
		private ?DateTime $closedAt,
		private Status $status,
		private Customer $customer,
		private Contract $contract,
		private DateTime $requestDeliveryAt,
	)
	{
		$UTC = new DateTimeZone("UTC");
		$this->createdAt->setTimezone($UTC);
		if ($this->closedAt) {
			$this->closedAt->setTimezone($UTC);
		}

	}

	public function getId(): int
	{
		return $this->id;
	}

	public function setId(int $id): void
	{
		$this->id = $id;
	}

	public function getOrderNumber(): string
	{
		return $this->orderNumber;
	}

	public function setOrderNumber(string $orderNumber): void
	{
		$this->orderNumber = $orderNumber;
	}

	public function getCreatedAt(): DateTime
	{
		return $this->createdAt;
	}

	public function setCreatedAt(DateTime $createdAt): void
	{
		$this->createdAt = $createdAt;
	}

	public function getClosedAt(): ?DateTime
	{
		return $this->closedAt;
	}

	public function setClosedAt(DateTime $closedAt): void
	{
		$this->closedAt = $closedAt;
	}

	public function getStatus(): Status
	{
		return $this->status;
	}

	public function setStatus(Status $status): void
	{
		$this->status = $status;
	}

	public function getCustomer(): Customer
	{
		return $this->customer;
	}

	public function setCustomer(Customer $customer): void
	{
		$this->customer = $customer;
	}

	public function getContract(): Contract
	{
		return $this->contract;
	}

	public function setContract(Contract $contract): void
	{
		$this->contract = $contract;
	}

	public function getRequestDeliveryAt(): DateTime
	{
		return $this->requestDeliveryAt;
	}

	public function setRequestDeliveryAt(DateTime $requestDeliveryAt): void
	{
		$this->requestDeliveryAt = $requestDeliveryAt;
	}

	/**
	 * @param $deep - when true, it will expand inner parameters into array, otherwise it will just print ids of object
	 * @return array
	 */
	public function toArray($deep = false): array
	{
		$array = [
			"id" => $this->getId(),
			"orderNumber" => $this->getOrderNumber(),
			"createdAt" => $this->getCreatedAt()->format("Y-m-dTH:i:sP"),
			"closedAt" => $this->getClosedAt()->format("Y-m-dTH:i:sP"),
			"requestedDeliveryAt" => $this->getRequestDeliveryAt()->format("Y-m-dTH:i:sP"),
		];

		if ($deep) {
			$objectsArray = [
				"status" => $this->getStatus()->toArray($deep),
				"customer" => $this->getCustomer()->toArray(),
				"contract" => $this->getContract()->toArray(),
			];
		} else {
			$objectsArray  = [
				"status" => $this->getStatus()->getId(),
				"customer" => $this->getCustomer()->getId(),
				"contract" => $this->getContract()->getId(),
			];
		}
		return array_merge($array, $objectsArray);
		
	}


}
