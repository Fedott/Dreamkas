<?php

namespace Lighthouse\CoreBundle\MongoDB\Types;

use Lighthouse\CoreBundle\Types\Date\DateTimestamp;
use MongoDate;
use DateTime;
use DateTimeZone;
use InvalidArgumentException;

class DateTimeUTCType extends BaseType
{
    const NAME = 'DateTimeUTC';

    /**
     * @param DateTime $value
     * @return MongoDate|null
     */
    public static function convertToMongo($value)
    {
        if ($value === null) {
            return null;
        } elseif ($value instanceof DateTime) {
            return new MongoDate($value->format('U'));
        }
        throw new \InvalidArgumentException(
            sprintf('Could not convert %s to a date value', is_scalar($value) ? '"'.$value.'"' : gettype($value))
        );
    }

    /**
     * @param MongoDate $value
     * @return DateTimestamp|null
     */
    public static function convertToPhp($value)
    {
        if ($value instanceof MongoDate) {
            $date = DateTimestamp::createFromMongoDate($value);
            $date->setTimezone(new DateTimeZone('UTC'));
            return $date;
        } else {
            return null;
        }
    }
}
