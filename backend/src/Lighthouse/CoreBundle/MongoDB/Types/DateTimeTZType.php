<?php

namespace Lighthouse\CoreBundle\MongoDB\Types;

use Lighthouse\CoreBundle\Types\Date\DateTimestamp;
use MongoDate;
use DateTime;
use DateTimeZone;

class DateTimeTZType extends BaseType
{
    const NAME = 'datetime_tz';

    /**
     * @param DateTime|null $value
     * @return array|null
     */
    public static function convertToMongo($value)
    {
        if ($value instanceof DateTime) {
            $utc = new DateTimestamp($value->format('Y-m-d\TH:i:s'), new DateTimeZone('UTC'));
            $dbValue = array(
                'date'   => new MongoDate($value->getTimestamp()),
                'year'   => (int) $value->format('Y'),
                'month'  => (int) $value->format('n'),
                'day'    => (int) $value->format('j'),
                'hour'   => (int) $value->format('G'),
                'minute' => (int) $value->format('i'),
                'second' => (int) $value->format('s'),
                'tz'     => $value->getTimezone()->getName(),
                'offset' => $value->getOffset(),
                'iso'    => $value->format(DateTime::ISO8601),
                'dayDate' => $utc->copy()->setTime(0, 0)->getMongoDate(),
                'hourDate' => $utc->copy()->setMinutes(0)->setSeconds(0)->getMongoDate()
            );
        } else {
            $dbValue = null;
        }
        return $dbValue;
    }

    /**
     * @param array|MongoDate[]|string[] $value
     * @return DateTime|null
     */
    public static function convertToPhp($value)
    {
        if (is_array($value) && isset($value['iso'])) {
            $date = new DateTime($value['iso']);
        } else {
            $date = null;
        }
        return $date;
    }
}
