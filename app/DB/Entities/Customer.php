<?php

declare(strict_types=1);

namespace App\DB\Entities;

/**
 * @phpstan-type CustomerArray array{id: int, name: string}
 */
class Customer
{
    public function __construct(
        private int $id,
        private string $name,
    ) {

    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return CustomerArray
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
        ];
    }
}
