<?php

declare(strict_types=1);

namespace App\UI\Home;

use App\UI\common\BasePresenter;
use JetBrains\PhpStorm\NoReturn;
use Nette;
use Tracy\Debugger;
use Tracy\ILogger;

final class HomePresenter extends BasePresenter
{
	#[NoReturn] public function actionDetail() : void
	{
		Debugger::log("Test", ILogger::INFO);
		die();
	}
}
