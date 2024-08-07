<?php

declare(strict_types=1);

namespace App\UI\Common;

use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\DefaultTemplate;

/**
 * @property DefaultTemplate $template
 */
class BasePresenter extends Presenter
{
    public function startup(): void
    {
        parent::startup();
        if (!isset($this->template->basePath)) {
            $this->template->add('basePath', '');
        }
    }
}
