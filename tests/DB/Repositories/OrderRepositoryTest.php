<?php
declare(strict_types=1);

namespace Test\DB\Repositories;

use _PHPStan_01e5828ef\Nette\Neon\Exception;
use App\DB\Entities\Contract;
use App\DB\Entities\Customer;
use App\DB\Entities\Order;
use App\DB\Entities\Status;
use App\DB\Entities\User;
use App\DB\Repositories\OrderRepository;
use Nette\Utils\DateTime;
use PHPUnit\Framework\TestCase;
use Tester\TestCaseException;

/**
 * @phpstan-import-type OrderArray from Order
 */
class OrderRepositoryTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testChangeStatusSameState()
    {
        // === SETUP DATA ==
        $status = new Status('TEST','test', new DateTime(), new User('test', 'test'));
        $customer = new Customer(2, 'TEST-CUSTOMER');
        $contract = new Contract(2, 'TEST-CUSTOMER', $customer);
        $order = new Order(1,'ON123456-TEST', new DateTime(), null, $status, $customer, $contract, new DateTime());

        // === CREATE MOCK ==
        $mock = $this->createPartialMock(OrderRepository::class, ['forceSave', 'get']);

        $mock->expects($this->never())
            ->method('forceSave')
            ->willThrowException(new Exception('This should not call forceSave. Status is the same'));

        $mock->expects($this->any())
            ->method('get')
            ->willReturn($order->toArray(true));


        // === TEST ==
        foreach ([new DateTime(), new DateTime('2024-01-01 0:00:00')] as $date) {
            $testStatus = clone $status;
            $testStatus->setCreatedAt($date);
            $mock->changeStatus(1, $status);
        }

        foreach ([new User('TEST-1', 'TEST-1'), new User('TEST-2', 'TEST-2')] as $user) {
            $testStatus = clone $status;
            $testStatus->setUser($user);
            $mock->changeStatus(1, $status);
        }

        $this->assertTrue(true);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testChangeStatusOtherState()
    {
        // === SETUP DATA ==
        $status = new Status('TEST','TEST', new DateTime(), new User('test', 'test'));
        $customer = new Customer(2, 'TEST-CUSTOMER');
        $contract = new Contract(2, 'TEST-CUSTOMER', $customer);
        $order = new Order(1,'ON123456-TEST', new DateTime(), null, $status, $customer, $contract, new DateTime());


        // === CREATE MOCK ==
        $mock = $this->createPartialMock(OrderRepository::class, ['forceSave', 'get']);


        $mock->expects($this->any())
            ->method('forceSave')
            ->willThrowException(new TestCaseException('This should not call forceSave. Status is the same'));

        $mock->expects($this->any())
            ->method('get')
            ->willReturn($order->toArray(true));


        // === TEST ==
        $this->expectException(TestCaseException::class);

        $newStatus = new Status($status->getId().'junk', $status->getName().'junk', $status->getCreatedAt(), $status->getUser());
        $mock->changeStatus(1, $newStatus);
    }
}
