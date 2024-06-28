<?php

namespace App\Facades;

use App\Entities\Contract;
use App\Entities\Customer;
use App\Entities\Order;
use App\Entities\Status;
use App\Entities\User;
use App\Enums\StatusEnum;
use App\Repositories\ContractsRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\OrderRepository;
use Nette\InvalidArgumentException;
use Nette\Utils\DateTime;

class ORMEntityFacade
{
	public function __construct(
		private readonly OrderRepository $orderRepository,
		private readonly CustomerRepository $customerRepository,
		private readonly ContractsRepository $contractsRepository,
	)
	{
	}

	public function createOrder(array $data) : Order
	{
		$customer = $this->getCustomer($data['customer']);
		$contract = $this->getContract($data['customer']);

		return new Order(
			$data['id'],
			$data['orderNumber'],
			$data['createdAt'],
			$data['closedAt'],
			$this->createStatus($data['status']),
			$customer,
			$contract,
			$data['deliveryAt'],
		);
	}

	public function createStatus(string $statusId) : Status
	{
		$status = StatusEnum::from($statusId);
		return new Status($status->value, $status->name, new DateTime(), $this->createUser());
	}

	public function createCustomer(array $data) : Customer
	{
		return new Customer(
			$data['id'],
			$data['name'],
		);
	}

	public function createContract(array $data, ?Customer $customer = null) : Contract
	{
		if (is_null($customer) && (!isset($data['customer']) || !is_int($data['customer']))) {
			throw new InvalidArgumentException('It is necessary to provide $customer or has customer id (int) in $data');
		}

		return new Contract(
			$data['id'],
			$data['name'],
			$customer ?? $this->getCustomer($data['customer']),
		);
	}

	public function createUser(array $data = []) : User
	{
		return new User('vojtech.sejkora', 'Vojtěch Sejkora');
	}

	public function getCustomer(int $customerId) : Customer
	{
		$data = $this->customerRepository->get($customerId);
		return $this->createCustomer($data);
	}

	public function getContract(int $contractId) : Contract
	{
		$data = $this->contractsRepository->get($contractId);
		return $this->createContract($data);
	}

	public function getOrder(int $orderId) : Order
	{
		$data = $this->orderRepository->get($orderId);
		return $this->createOrder($data);
	}

	/**
	 * @return array<Order>
	 */
	public function getOrders() : array
	{
		$data = $this->orderRepository->getAll();
		$orders = [];
		foreach ($data as $item) {
			$orders[] = $this->createOrder($item);
		}
		return $orders;
	}
}
