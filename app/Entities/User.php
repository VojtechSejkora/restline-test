<?php

namespace App\Entities;
class User
{

	public function __construct(
		private string $userName,
		private string $fullName,
	)
	{
	}

	public function getUserName(): string
	{
		return $this->userName;
	}

	public function setUserName(string $userName): void
	{
		$this->userName = $userName;
	}

	public function getFullName(): string
	{
		return $this->fullName;
	}

	public function setFullName(string $fullName): void
	{
		$this->fullName = $fullName;
	}



}
