<?php

namespace App\DB\Entities;

use App\DB\Utils\DateTimeConverter;
use Nette\Utils\DateTime;

class Status
{
	private DateTime $createdAt;

	public function __construct(
		private string $id,
		private string $name,
		DateTime|string $createdAt,
		private User $user
	)
	{
		$this->createdAt = DateTimeConverter::createDateTime($createdAt);
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function setId(string $id): void
	{
		$this->id = $id;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function getCreatedAt(): string
	{
		return $this->createdAt;
	}

	public function setCreatedAt(DateTime $createdAt): void
	{
		$this->createdAt = $createdAt;
	}

	public function getUser(): User
	{
		return $this->user;
	}

	public function setUser(User $user): void
	{
		$this->user = $user;
	}

	public function toArray($deep = false): array
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'createdAt' => $this->createdAt,
			'user' => [
				"userName" => $this->user->getUserName(),
				"firstName" => $this->user->getFullName(),
			]
		];
	}
}
