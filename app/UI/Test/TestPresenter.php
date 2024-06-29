<?php

namespace App\UI\Test;

use App\DB\Entities\Contract;
use App\DB\Entities\Customer;
use App\DB\Entities\Order;
use App\DB\Entities\Status;
use App\DB\Entities\User;
use App\DB\Repositories\OrderRepository;
use App\UI\Common\BasePresenter;
use Nette\Utils\DateTime;

class TestPresenter extends BasePresenter
{

    public function __construct(
        private readonly OrderRepository $orderRepository,
    )
    {
        parent::__construct();
    }

    public function actionDefault() : void
    {
        $status = new Status('TEST','TEST', new DateTime(), new User('test', 'test'));
        $customer = new Customer(2, 'TEST-CUSTOMER');
        $contract = new Contract(2, 'TEST-CUSTOMER', $customer);
        $order = new Order(1,'ON123456-TEST', new DateTime(), null, $status, $customer, $contract, new DateTime());

        $this->orderRepository->changeStatus(203, $status);
        die();
    }
}
