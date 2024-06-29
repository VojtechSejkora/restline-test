<?php

declare(strict_types=1);

namespace App\UI\Home;

use App\UI\Common\BasePresenter;

final class HomePresenter extends BasePresenter
{
    public function startup(): void
    {
        parent::startup();
        $this->redirect('Order:default');
    }
}
