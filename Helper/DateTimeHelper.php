<?php

namespace App\Helper;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;

class DateTimeHelper
{
    public const DATE_FORMAT = 'd.m.Y';
    public const DATETIME_FORMAT = 'd.m.Y H:i:s';
    public const DATETIME_SHORT_FORMAT = 'd.m.Y H:i';

    public static function firstDayOfThisYear(): bool|DateTimeImmutable|null
    {
        return new DateTimeImmutable("first day of january last year");
    }

    public static function lastDayOfThisYear(): bool|DateTimeImmutable|null
    {
        $date = new DateTimeImmutable("last day of december last year");
        return $date->setTime(23, 59, 59);
    }

    public static function firstDayOfWeek(?int $offset = 0): DateTime {
        $date = new DateTime();
        $date->modify('Monday this week');
        $date->modify("$offset week");
        return $date;
    }

    public static function lastDayOfWeek(?int $offset = 0): DateTime {
        $date = new DateTime();
        $date->modify('Sunday this week');
        $date->modify("$offset week");
        return $date;
    }

    public static function firstDayOfMonth(?int $offset = 0): DateTime {
        $date = new DateTime();
        $date->modify('first day of this month');
        $date->modify("$offset month");
        return $date;
    }


    public static function lastDayOfMonth(?int $offset = 0): DateTime {
        $date = new DateTime();
        $date->modify('last day of this month');
        $date->modify("$offset month");
        return $date;
    }

    public static function firstDayOfPreviousWeek(): DateTime {
        return self::firstDayOfWeek(-1);
    }

    public static function lastDayOfPreviousWeek(): DateTime {
        return self::lastDayOfWeek(-1);
    }

    public static function convertDateType(
        $propType,
        $value,
        ?string $format = null
    ): null|DateTime|DateTimeImmutable {
        switch ($propType) {
            case "date":
                $properFormat = ($format) ?: self::DATE_FORMAT;
                $convertResult = DateTime::createFromFormat($properFormat, $value);
                break;
            case "datetime":
                $properFormat = ($format) ?: self::DATETIME_FORMAT;
                $convertResult = DateTime::createFromFormat($properFormat, $value);
                break;
            case "DateTimeImmutable":
                $properFormat = ($format) ?: self::DATETIME_FORMAT;
                $convertResult = DateTimeImmutable::createFromFormat($properFormat, $value);
                break;
            default:
                $properFormat = ($format) ?: self::DATE_FORMAT;
                $convertResult = DateTime::createFromFormat($properFormat, $value);
        }
        return (false === $convertResult) ? null : $convertResult;
    }

    public static function createDateTime(
        string $timeString,
        ?string $format = null,
        ?bool $immutable = true
    ): bool|DateTime|DateTimeImmutable|null {
        $dateTimeFormat = ($format) ?: self::DATETIME_FORMAT;
        return ($immutable)
            ? DateTimeImmutable::createFromFormat($dateTimeFormat, $timeString)
            : DateTime::createFromFormat($dateTimeFormat, $timeString);
    }

    public static function createDateTimeAndModify(
        ?string $timeString = null,
        string $modifier = '',
        ?string $format = null
    ): ?DateTimeImmutable {
        $newDatetime = self::createDateTime($timeString, $format);
        return ($newDatetime) ? $newDatetime->modify($modifier) : null;
    }

    public static function generateDateRange(
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
        bool $arrayOfDatesObject = false
    ): array {
        $dateArray = [];
        $currentDate = clone $startDate;

        while ($currentDate <= $endDate) {
            if ($arrayOfDatesObject) {
                $dateArray[$currentDate->format('d.m.Y')] = clone $currentDate;
            } else {
                $dateArray[] = $currentDate->format('d.m.Y');
            }
            $currentDate->modify('+1 day');
        }

        return $dateArray;
    }

    /**
     * @throws Exception
     */
    public static function subtractIntervals(DateInterval $interval, DateInterval $intervalToSubtract): DateInterval
    {
        $time = self::DateIntervalToSeconds($interval);
        $timeToSubtract = self::DateIntervalToSeconds($intervalToSubtract);

        $time -= $timeToSubtract;

        return self::validateDateTimeInterval(new DateInterval("PT{$time}S"));
    }

    /**
     * @throws Exception
     */
    public static function addIntervals(DateInterval $interval, DateInterval $intervalToAdd): DateInterval
    {
        $time = self::DateIntervalToSeconds($interval);
        $timeToAdd = self::DateIntervalToSeconds($intervalToAdd);

        $time += $timeToAdd;

        return self::validateDateTimeInterval(new DateInterval("PT{$time}S"));
    }

    public static function createZeroDateInterval(): DateInterval
    {
        return new DateInterval('P0Y0M0DT0H0M0S');
    }

    /**
     * @throws Exception
     */
    public static function validateDateTimeInterval(DateInterval $interval): DateInterval
    {
        // Extract properties
        $years = $interval->y;
        $months = $interval->m;
        $days = $interval->d;
        $hours = $interval->h;
        $minutes = $interval->i;
        $seconds = $interval->s;

        // Validate and correct seconds
        while ($seconds >= 60) {
            $seconds -= 60;
            $minutes++;
        }

        // Validate and correct minutes
        while ($minutes >= 60) {
            $minutes -= 60;
            $hours++;
        }

        // Validate and correct hours
        while ($hours >= 24) {
            $hours -= 24;
            $days++;
        }

        $correctedIntervalSpec = "P{$years}Y{$months}M{$days}DT{$hours}H{$minutes}M{$seconds}S";
        return new DateInterval($correctedIntervalSpec);
    }

    /**
     * @throws Exception
     */
    public static function createHourlyDateIntervalFromString(string $timeString): DateInterval
    {
        list($hours, $minutes) = explode(':', $timeString);
        $intervalSpec = "PT" . (int)$hours . "H" . (int)$minutes . "M";
        return new DateInterval($intervalSpec);
    }

    private static function DateIntervalToSeconds(DateInterval $interval): float|int
    {
        return $interval->s +
            ($interval->i * 60) +
            ($interval->h * 3600) +
            ($interval->d * 24 * 60 * 60) +
            ($interval->m * 30 * 24 * 60 * 60) +
            ($interval->y * 365 * 24 * 60 * 60);
    }

    /**
     * @param DateInterval $interval1
     * @param DateInterval $interval2
     * @return int
     */
    public static function compareDateIntervals(DateInterval $interval1, DateInterval $interval2): int
    {
        $seconds1 = self::DateIntervalToSeconds($interval1);
        $seconds2 = self::DateIntervalToSeconds($interval2);

        if ($seconds1 > $seconds2) {
            return 1;  // interval1 is longer
        } elseif ($seconds1 < $seconds2) {
            return -1; // interval2 is longer
        } else {
            return 0;  // intervals are equal
        }
    }


}