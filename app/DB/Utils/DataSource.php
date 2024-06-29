<?php

namespace App\DB\Utils;

use App\DB\Facades\Error;
use Closure;
use Nette\Utils\Strings;
use Tracy\Debugger;
use Ublaboo\DataGrid\DataSource\IDataSource;
use Ublaboo\DataGrid\Utils\Sorting;

class DataSource implements IDataSource
{
	/**
	 * @param array<int, mixed|null> $data
	 */
	public function __construct(
		private array $data
	)
	{
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
		// TODO: Implement filter() method.
	}

	/**
	 * @param array<string, mixed> $condition
	 * @return IDataSource
	 */
	public function filterOne(array $condition): IDataSource
	{
		foreach ($this->data as $item) {
			$all = True;
			foreach ($condition as $column => $value) {
				if ($this->get($item, $column) != $value) {
					$all = false;
					break;
				}
			}
			if ($all) {
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
	private function _sortCall(array $sortType) : Closure
	{
		foreach ($sortType as $key => $order) {
			// potentially improve to allow multiple sorting columns
			return function ($objA, $objB) use ($key, $order) {
				$a = $this->get($objA, $key);
				$b = $this->get($objB, $key);

				if ($a == $b) {
					return 0;
				}

				return (($a < $b) ? -1 : 1) * (($order == 'ASC') ? 1 : -1);
			};
		}
		return fn($a, $b) => 0;
	}
}
