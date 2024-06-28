<?php

namespace App\DB\Utils;

use DateTimeZone;
use Nette\Utils\DateTime;

class DateTimeConverter
{
	public static function createDateTime(DateTime|string|null $date) {

		if ($date == null) {
			return $date;
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
}
