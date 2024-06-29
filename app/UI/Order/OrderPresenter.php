<?php

declare(strict_types=1);

namespace App\UI\Order;

use App\DB\Facades\ORMEntityFacade;
use App\UI\Common\BasePresenter;
use Nette;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;

class OrderPresenter extends BasePresenter
{
    private ?int $orderId = null;

    public function __construct(
        private readonly OrderDataGridFactory $orderDataGridFactory,
        private readonly OrderEditFormFactory $orderEditFormFactory,
        private readonly ORMEntityFacade $ORMEntityFacade,
    ) {
        parent::__construct();
    }

    public function actionEdit(int $id): void
    {
        $this->orderId = $id;
    }

    public function createComponentOrderDataGrid(): ?Nette\ComponentModel\IComponent
    {
        return $this->orderDataGridFactory->create();
    }

    /**
     * @throws InvalidLinkException
     */
    public function createComponentOrderEditForm(): Form
    {
        if ($this->orderId === null) {
            throw new \LogicException("There should be set orderId from actionDetail");
        }

        $order = $this->ORMEntityFacade->getOrder($this->orderId);

        if ($order === null) {
            $this->error('Not found order id');
        }

        return $this->orderEditFormFactory->create($order, $this->link('loadContract!', '#'));
    }

    public function handleLoadContract(string $customerId): void
    {
        $this->sendJson($this->orderEditFormFactory->loadContracts((int) $customerId));
    }
}
