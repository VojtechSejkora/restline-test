<?php

declare(strict_types=1);

namespace App\DB\Utils;

use Closure;
use Nette\Utils\Strings;
use Ublaboo\DataGrid\DataSource\IDataSource;
use Ublaboo\DataGrid\Utils\Sorting;

class DataSource implements IDataSource
{
    /**
     * @param array<int, mixed|null> $data
     */
    public function __construct(
        private array $data
    ) {
    }

    public function getCount(): int
    {
        return count($this->data);
    }

    /**
     * @return array<null|mixed>
     */
    public function getData(): iterable
    {
        return $this->data;
    }

    public function filter(array $filters): void
    {
        $newData = [];
        foreach ($this->data as $item) {
            if ($this->satisfyFilter($item, $filters)) {
                $newData[] = $item;
            }
        }
        $this->data = $newData;
    }

    /**
     * @param array<string, mixed> $condition
     * @return IDataSource
     */
    public function filterOne(array $condition): IDataSource
    {
        foreach ($this->data as $item) {
            if ($this->satisfyFilter($item, $condition)) {
                $this->data = [$item];
                return $this;
            }
        }
        $this->data = [];
        return $this;
    }

    public function limit(int $offset, int $limit): IDataSource
    {
        $this->data = array_slice($this->data, $offset, $limit);
        return $this;
    }

    public function sort(Sorting $sorting): IDataSource
    {
        usort($this->data, $this->_sortCall($sorting->getSort()));
        return $this;
    }

    /**
     * @param object $item
     * @param array<string, mixed> $filters
     * @return bool
     */
    private function satisfyFilter(object $item, array $filters): bool
    {
        foreach ($filters as $column => $value) {
            if ($this->get($item, $column) !== $value) {
                return false;
            }
        }
        return true;
    }

    public static function get(object $obj, string $column): null|object|string|int|float
    {
        foreach (Strings::split($column, '[\.]') as $selector) {
            $getMethod = 'get' . ucfirst($selector);
            $obj = $obj->$getMethod();
        }

        return $obj;
    }

    /**
     * @param array<string, string> $sortType
     */
    private function _sortCall(array $sortType): Closure
    {
        foreach ($sortType as $key => $order) {
            // potentially improve to allow multiple sorting columns
            return function ($objA, $objB) use ($key, $order) {
                $a = $this->get($objA, $key);
                $b = $this->get($objB, $key);

                if ($a === $b) {
                    return 0;
                }

                return (($a < $b) ? -1 : 1) * (($order === 'ASC') ? 1 : -1);
            };
        }
        return fn ($a, $b) => 0;
    }
}
