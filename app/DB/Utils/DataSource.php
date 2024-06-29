<?php

declare(strict_types=1);

namespace App\DB\Utils;

use Closure;
use DateTime;
use Nette\Utils\Strings;
use Ublaboo\DataGrid\DataSource\IDataSource;
use Ublaboo\DataGrid\Exception\DataGridDateTimeHelperException;
use Ublaboo\DataGrid\Filter\Filter;
use Ublaboo\DataGrid\Filter\FilterDate;
use Ublaboo\DataGrid\Filter\FilterDateRange;
use Ublaboo\DataGrid\Filter\FilterMultiSelect;
use Ublaboo\DataGrid\Filter\FilterRange;
use Ublaboo\DataGrid\Filter\FilterSelect;
use Ublaboo\DataGrid\Filter\FilterText;
use Ublaboo\DataGrid\Utils\DateTimeHelper;
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
        foreach ($filters as $filter) {
            if ($filter->isValueSet()) {
                if ($filter->getConditionCallback() !== null) {
                    $data = (array) call_user_func_array(
                        $filter->getConditionCallback(),
                        [$this->data, $filter->getValue()]
                    );
                    $this->setData($data);
                } else {
                    $data = array_filter(
                    /**
                     * @throws DataGridDateTimeHelperException
                     */ $this->data, function ($row) use ($filter) {
                        return $this->applyFilter($row, $filter);
                    });
                    $this->setData($data);
                }
            }
        }
    }


    /**
     * {@inheritDoc}
     */
    public function filterOne(array $condition): IDataSource
    {
        foreach ($this->data as $item) {
            if ($this->applyCondition($item, $condition)) {
                $this->setData([$item]);

                return $this;
            }
        }

        $this->setData([]);
    }

    public function limit(int $offset, int $limit): IDataSource
    {
        $this->data = array_slice($this->data, $offset, $limit);
        return $this;
    }

    public function sort(Sorting $sorting): IDataSource
    {
        usort($this->data, $this->sortCall($sorting->getSort()));
        return $this;
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
    private function sortCall(array $sortType): Closure
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

    private function setData(array $array)
    {
        $this->data = $array;
    }

    /**
     * @throws DataGridDateTimeHelperException
     */
    protected function applyFilter(object $row, Filter $filter) : object|false
    {

        if ($filter instanceof FilterDate) {
            return $this->applyFilterDate($row, $filter);
        }

        if ($filter instanceof FilterMultiSelect) {
            return $this->applyFilterMultiSelect($row, $filter);
        }

        if ($filter instanceof FilterDateRange) {
            return $this->applyFilterDateRange($row, $filter);
        }

        if ($filter instanceof FilterRange) {
            return $this->applyFilterRange($row, $filter);
        }

        $condition = $filter->getCondition();
        return $this->applyCondition($row, $condition, $filter);
    }

    private function applyCondition(object $row, array $condition, Filter|null $filter = null): object|false
    {
        foreach ($condition as $column => $value) {
            $value = (string) $value;
            $rowVal = (string) DataSource::get($row, $column);

            if ($filter instanceof FilterSelect) {
                return $rowVal === $value;
            }

            if ($filter instanceof FilterText && $filter->isExactSearch()) {
                return $rowVal === $value;
            }

            $words = $filter instanceof FilterText && $filter->hasSplitWordsSearch() === false ? [$value] : explode(' ', $value);

            $rowVal = strtolower(Strings::toAscii($rowVal));

            foreach ($words as $word) {
                if (str_contains($rowVal, strtolower(Strings::toAscii($word)))) {
                    return $row;
                }
            }
        }
        return false;
    }


    /**
     * @param mixed $row
     */
    protected function applyFilterMultiSelect($row, FilterMultiSelect $filter): bool
    {
        $condition = $filter->getCondition();
        $values = $condition[$filter->getColumn()];

        return in_array($row[$filter->getColumn()], $values, true);
    }


    /**
     * @param mixed $row
     */
    protected function applyFilterRange($row, FilterRange $filter): bool
    {
        $condition = $filter->getCondition();
        $values = $condition[$filter->getColumn()];

        if ($values['from'] !== null && $values['from'] !== '') {
            if ($values['from'] > $row[$filter->getColumn()]) {
                return false;
            }
        }

        if ($values['to'] !== null && $values['to'] !== '') {
            if ($values['to'] < $row[$filter->getColumn()]) {
                return false;
            }
        }

        return true;
    }


    /**
     * @param mixed $row
     */
    protected function applyFilterDateRange($row, FilterDateRange $filter): bool
    {
        $format = $filter->getPhpFormat();
        $condition = $filter->getCondition();
        $values = $condition[$filter->getColumn()];
        $row_value = $row[$filter->getColumn()];

        if ($values['from'] !== null && $values['from'] !== '') {
            $date_from = DateTimeHelper::tryConvertToDate($values['from'], [$format]);
            $date_from->setTime(0, 0, 0);

            if (!($row_value instanceof DateTime)) {
                /**
                 * Try to convert string to DateTime object
                 */
                try {
                    $row_value = DateTimeHelper::tryConvertToDate($row_value);
                } catch (DataGridDateTimeHelperException $e) {
                    /**
                     * Otherwise just return raw string
                     */
                    return false;
                }
            }

            if ($row_value->getTimestamp() < $date_from->getTimestamp()) {
                return false;
            }
        }

        if ($values['to'] !== null && $values['to'] !== '') {
            $date_to = DateTimeHelper::tryConvertToDate($values['to'], [$format]);
            $date_to->setTime(23, 59, 59);

            if (!($row_value instanceof DateTime)) {
                /**
                 * Try to convert string to DateTime object
                 */
                try {
                    $row_value = DateTimeHelper::tryConvertToDate($row_value);
                } catch (DataGridDateTimeHelperException $e) {
                    /**
                     * Otherwise just return raw string
                     */
                    return false;
                }
            }

            if ($row_value->getTimestamp() > $date_to->getTimestamp()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Apply fitler date and tell whether row value matches or not
     * @param mixed $row
     * @throws DataGridDateTimeHelperException
     */
    protected function applyFilterDate($row, FilterDate $filter): bool
    {
        $format = $filter->getPhpFormat();
        $condition = $filter->getCondition();

        foreach ($condition as $column => $value) {
            $row_value = $row[$column];

            $date = DateTimeHelper::tryConvertToDateTime($value, [$format]);

            if (!($row_value instanceof DateTime)) {
                /**
                 * Try to convert string to DateTime object
                 */
                try {
                    $row_value = DateTimeHelper::tryConvertToDateTime($row_value);
                } catch (DataGridDateTimeHelperException $e) {
                    /**
                     * Otherwise just return raw string
                     */
                    return false;
                }
            }

            return $row_value->format($format) === $date->format($format);
        }

        return false;
    }
}
