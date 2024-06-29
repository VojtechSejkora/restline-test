<?php

declare(strict_types=1);

namespace App\UI\Error\Error4xx;

use App\UI\Common\BasePresenter;
use Nette;
use Nette\Application\Attributes\Requires;


/**
 * Handles 4xx HTTP error responses.
 */
#[Requires(methods: '*')]
final class Error4xxPresenter extends BasePresenter
{
	public function renderDefault(Nette\Application\BadRequestException $exception): void
	{
		// renders the appropriate error template based on the HTTP status code
		$code = $exception->getCode();
		$file = is_file($file = __DIR__ . "/$code.latte")
			? $file
			: __DIR__ . '/4xx.latte';
		if (!isset($this->template->httpCode)) {
			$this->template->add('httpCode', $code);
		} else {
			$this->template->httpCode = $code;
		}
		$this->template->setFile($file);
	}
}
