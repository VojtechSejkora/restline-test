<?php

namespace App\Entities;

class Status
{

	public function __construct(
		private int $id,
		private string $name,
	)
	{
	}
}
