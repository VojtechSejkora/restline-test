<?php

namespace App\DB\Entities;

use App\DB\Utils\DateTimeConverter;
use DateTimeImmutable;
use DateTimeZone;
use Nette\Utils\DateTime;

class Order
{
	private DateTime $createdAt;
	private ?Datetime $closedAt = null;

	private Datetime $requestDeliveryAt;

	public function __construct(
		private int $id,
		private string $orderNumber,
		DateTimeImmutable|DateTime|string $createdAt,
		DateTimeImmutable|DateTime|null|string $closedAt,
		private Status $status,
		private Customer $customer,
		private Contract $contract,
		DateTimeImmutable|DateTime|string $requestedDeliveryAt,
	)
	{
		$this->createdAt = DateTimeConverter::createDateTime($createdAt);
		$this->closedAt = DateTimeConverter::createDateTime($closedAt);
		$this->requestDeliveryAt = DateTimeConverter::createDateTime($requestedDeliveryAt);
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

	public function getRequestedDeliveryAt(): DateTime
	{
		return $this->requestDeliveryAt;
	}

	public function setRequestedDeliveryAt(DateTime $requestDeliveryAt): void
	{
		$this->requestDeliveryAt = $requestDeliveryAt;
	}

	/**
	 * @param false $deep - when true, it will expand inner parameters into array, otherwise it will just print ids of object
	 * @return array
	 */
	public function toArray(bool $deep = false): array
	{
		$array = [
			"id" => $this->getId(),
			"orderNumber" => $this->getOrderNumber(),
			"createdAt" =>  DateTimeConverter::toSerialize($this->getCreatedAt()),
			"closedAt" =>  DateTimeConverter::toSerialize($this->getClosedAt()),
			"requestedDeliveryAt" => DateTimeConverter::toSerialize($this->getRequestedDeliveryAt()),
			"customer" => $this->getCustomer()->getId(),
			"contract" => $this->getContract()->getId(),

		];

		if ($deep) {
			$objectsArray = [
				"status" => $this->getStatus()->toArray(),
			];
		} else {
			$objectsArray  = [
				"status" => $this->getStatus()->getId(),
			];
		}
		return array_merge($array, $objectsArray);
	}




}
