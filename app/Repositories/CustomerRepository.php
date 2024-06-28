<?php

namespace App\Repositories;

class CustomerRepository
{

	public function getAll()
	{
		return [
			23 => 'Miroslav Novák',
			409 => "Jan Mikulovský",
			143 => "Linet",
		];
	}
}
