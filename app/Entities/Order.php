<?php

namespace App\Entities;

class Order
{

	public function __construct(
		private int $id,
		private string $name,
	)
	{
	}
}
