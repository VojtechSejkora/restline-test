<?php

namespace App\Entities;

use App\Enums\StatusEnum;
use Nette\Utils\DateTime;

class Status
{
	public function __construct(
		private int $id,
		private string $name,
		private DateTime $createdAt,
		private User $user
	)
	{
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function setId(int $id): void
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
