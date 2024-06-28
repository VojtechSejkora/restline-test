<?php

declare(strict_types=1);

namespace App\UI\Home;

use JetBrains\PhpStorm\NoReturn;
use Nette;
use Tracy\Debugger;
use Tracy\ILogger;

final class HomePresenter extends Nette\Application\UI\Presenter
{
	#[NoReturn] public function actionDetail() : void
	{
		Debugger::log("Test", ILogger::INFO);
		die();
	}
}
