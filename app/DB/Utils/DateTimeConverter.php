<?php

declare(strict_types=1);

namespace App\DB\Utils;

use DateTimeImmutable;
use DateTimeZone;
use Nette\Utils\DateTime;

class DateTimeConverter
{
    public static function createDateTime(DateTimeImmutable|DateTime|string|null $date): ?DateTime
    {
        if ($date === null) {
            return null;
        }

        if ($date instanceof DateTimeImmutable) {
            try {
                $date = DateTime::from($date);
            } catch (\Exception $e) {
                throw new \LogicException("Date cannot be converted", previous: $e);
            }
        }

        if ($date instanceof DateTime) {
            return $date->setTimezone(new DateTimeZone('UTC'));
        } else {
            return DateTime::createFromFormat(DATE_ATOM, $date, new DateTimeZone('UTC')) ?: null;
        }
    }

    public static function createNow(): DateTime
    {
        return new DateTime('now', new DateTimeZone('UTC'));
    }

    public static function format(?DateTime $time): ?string
    {
        if ($time === null) {
            return null;
        }

        $datetime = clone $time;
        $datetime->setTimezone(new DateTimeZone('Europe/Prague'));
        return $datetime->format('Y-m-d H:i:s');
    }

    public static function toSerialize(?DateTime $time): ?string
    {
        if ($time === null) {
            return null;
        }
        $datetime = clone $time;
        return $datetime->format(DATE_ATOM);
    }
}
