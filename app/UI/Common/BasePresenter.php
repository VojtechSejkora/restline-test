<?php

namespace App\UI\Common;

use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\DefaultTemplate;

/**
 * @property DefaultTemplate $template
 */
class BasePresenter extends Presenter
{
	function startup(): void
	{
		parent::startup();
		if (!$this->template->basePath) {
			$this->template->add('basePath', '');
		}
	}
}
