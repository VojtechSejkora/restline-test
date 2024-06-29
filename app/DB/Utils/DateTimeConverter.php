<?php

namespace App\DB\Utils;

use DateTimeImmutable;
use DateTimeZone;
use Nette\Utils\DateTime;
use Tracy\Debugger;

class DateTimeConverter
{
	public static function createDateTime(DateTimeImmutable|DateTime|string|null $date) : ?DateTime
	{

		if ($date == null) {
			return $date;
		}

		if ($date instanceof DateTimeImmutable) {
			$date = DateTime::from($date);
		}

		if ($date instanceof DateTime) {
			$date->setTimezone(new DateTimeZone('UTC'));
			return $date;
		} else {
			return DateTime::createFromFormat(DATE_ATOM, $date, new DateTimeZone('UTC'));
		}
	}

	public static function createNow()
	{
		return new DateTime('now', new DateTimeZone('UTC'));
	}

	public static function format(?DateTime $time)
	{
		if ($time == null) {
			return null;
		}

		$datetime = clone $time;
		$datetime->setTimezone(new DateTimeZone('Europe/Prague'));
		return $datetime->format('Y-m-d H:i:s');
	}

	public static function toSerialize(?DateTime $time)
	{
		if ($time == null) {
			return null;
		}
		$datetime = clone $time;
		return $datetime->format(DATE_ATOM);
	}
}
