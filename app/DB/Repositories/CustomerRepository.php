<?php

declare(strict_types=1);

namespace App\DB\Repositories;

use App\DB\Entities\Customer;
use Jajo\JSONDB;

/**
 * @phpstan-import-type CustomerArray from Customer
 */
class CustomerRepository
{
    public const DB_FILE = "customers.json";

    public function __construct(
        private JSONDB $db,
    ) {
    }

    /**
     * @return array<CustomerArray>
     */
    public function getAll(): array
    {
        return $this->db->select('*')
            ->from(self::DB_FILE)
            ->get();
    }

    /**
     * @param int $customerId
     * @return CustomerArray
     */
    public function get(int $customerId): array
    {
        $result = $this->db->select('*')
            ->from(self::DB_FILE)
            ->where([
                'id' => $customerId,
            ])
            ->get();

        if (count($result) === 1) {
            return $result[0];
        }
        return $result;
    }
}
