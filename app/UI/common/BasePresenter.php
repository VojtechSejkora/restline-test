<?php

namespace App\UI\common;

use Nette\Application\UI\Presenter;

class BasePresenter extends Presenter
{
	function beforeRender() {
		$this->template->basePath = '';
	}
}
