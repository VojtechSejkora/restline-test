<?php

namespace App\DB\Facades;

use App\DB\Entities\Contract;
use App\DB\Entities\Customer;
use App\DB\Entities\Order;
use App\DB\Entities\Status;
use App\DB\Entities\User;
use App\DB\Enums\StatusEnum;
use App\DB\Repositories\ContractsRepository;
use App\DB\Repositories\CustomerRepository;
use App\DB\Repositories\OrderRepository;
use Nette\InvalidArgumentException;
use Nette\Utils\DateTime;
use Tracy\Debugger;

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
		$contract = $this->getContract($data['contract']);

		Debugger::barDump($data);
		return new Order(
			$data['id'],
			$data['orderNumber'],
			$data['createdAt'],
			$data['closedAt'],
			$this->createStatus($data['status']),
			$customer,
			$contract,
			$data['requestedDeliveryAt'],
		);
	}

	public function createStatus(array|string $data) : Status
	{
		if (is_array($data)) {
			return new Status($data['id'], $data['name'], $data['createdAt'], $this->createUser($data['user']));
		}
		$status = StatusEnum::from($data);
		return new Status($status->value, $status->name, new DateTime(timezone: 'UTC'), $this->createUser());
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
		if (count($data) == 0) {
			return new User('vojtech.sejkora', 'Vojtěch Sejkora');
		} else {
			return new User($data['userName'], $data['fullName']);
		}
	}

	public function getCustomer(int $customerId) : Customer
	{
		$data = $this->customerRepository->get($customerId);
		Debugger::barDump($data);
		return $this->createCustomer($data);
	}

	public function getContract(int $contractId) : Contract
	{
		$data = $this->contractsRepository->get($contractId);
		Debugger::barDump($data);
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
