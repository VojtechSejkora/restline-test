<?php

declare(strict_types=1);

namespace App\UI\Home;

use App\UI\Common\BasePresenter;
use JetBrains\PhpStorm\NoReturn;
use Nette;
use Tracy\Debugger;
use Tracy\ILogger;

final class HomePresenter extends BasePresenter
{
	function startup(): void
	{
		parent::startup();
		$this->redirect('Order:default');
	}
}
