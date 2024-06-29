<?php

declare(strict_types=1);

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
use App\DB\Utils\DateTimeConverter;
use LogicException;
use Nette\InvalidArgumentException;

/**
 * @phpstan-import-type CustomerArray from Customer
 * @phpstan-import-type ContractArray from Contract
 * @phpstan-import-type OrderArray from Order
 * @phpstan-import-type StatusArray from Status
 */
readonly class ORMEntityFacade
{
    public function __construct(
        private OrderRepository $orderRepository,
        private CustomerRepository $customerRepository,
        private ContractsRepository $contractsRepository,
    ) {
    }

    /**
     * @param OrderArray $data
     * @return Order
     */
    public function createOrder(array $data): Order
    {
        $customer = $this->getCustomer($data['customer']);
        $contract = $this->getContract($data['contract']);

        return new Order(
            $data['id'],
            $data['orderNumber'],
            $data['createdAt'],
            $data['closedAt'] ?? null,
            $this->createStatus($data['status']),
            $customer,
            $contract,
            $data['requestedDeliveryAt'] ?? null,
        );
    }

    /**
     * @param StatusArray|string $data
     * @return Status
     */
    public function createStatus(array|string $data): Status
    {
        if (is_array($data)) {
            return new Status($data['id'], $data['name'], $data['createdAt'], $this->createUser($data['user']));
        }
        $status = StatusEnum::from($data);
        return new Status($status->value, $status->name, DateTimeConverter::createNow(), $this->createUser());
    }

    /**
     * @param CustomerArray $data
     * @return Customer
     */
    public function createCustomer(array $data): Customer
    {
        return new Customer(
            $data['id'],
            $data['name'],
        );
    }

    /**
     * @param array{id:int, name:string, customer?:int} $data
     * @param Customer|null $customer
     * @return Contract
     */
    public function createContract(array $data, ?Customer $customer = null): Contract
    {
        if (is_null($customer) && key_exists('customer', $data)) {
            $customer = $this->getCustomer($data['customer']);
        }
        if (is_null($customer)) {
            throw new InvalidArgumentException('It is necessary to provide $customer or has customer id (int) in $data');
        }

        return new Contract(
            $data['id'],
            $data['name'],
            $customer,
        );
    }

    /**
     * @param array{}|array{userName:string, fullName:string} $data
     * @return User
     */
    public function createUser(array $data = []): User
    {
        if (count($data) === 0) {
            return new User('vojtech.sejkora', 'Vojtěch Sejkora');
        } else {
            return new User($data['userName'], $data['fullName']);
        }
    }

    public function getCustomer(int $customerId): Customer
    {
        $data = $this->customerRepository->get($customerId);

        return $this->createCustomer($data);
    }

    public function getContract(int $contractId): Contract
    {
        $data = $this->contractsRepository->get($contractId);

        if (empty($data)) {
            throw new LogicException("Not found contract by contractId: {$contractId}");
        }
        return $this->createContract($data);
    }

    public function getOrder(int $orderId): Order
    {
        $data = $this->orderRepository->get($orderId);
        return $this->createOrder($data);
    }

    /**
     * @return array<Order>
     */
    public function getOrders(): array
    {
        $data = $this->orderRepository->getAll();
        $orders = [];
        foreach ($data as $item) {
            $orders[] = $this->createOrder($item);
        }
        return $orders;
    }

    /**
     * @return array<Customer>
     */
    public function getCustomers(): array
    {
        $data = $this->customerRepository->getAll();
        $customer = [];
        foreach ($data as $item) {
            $customer[] = $this->createCustomer($item);
        }
        return $customer;
    }

    /**
     * @param int $customerId
     * @return array<Contract>
     */
    public function getContractsByCustomerId(int $customerId): array
    {
        $data = $this->contractsRepository->getByCustomer($customerId);
        $contracts = [];
        foreach ($data as $item) {
            $contracts[] = $this->createContract($item);
        }
        return $contracts;
    }
}
