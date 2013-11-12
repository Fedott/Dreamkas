<?php

namespace Lighthouse\CoreBundle\Types;

use DateTime;
use MongoTimestamp;
use MongoDate;

class DateTimestamp extends DateTime
{
    /**
     * @var int
     */
    public $inc;



    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTimestamp();
    }

    /**
     * @return int
     */
    public function getUsec()
    {
        return (int) $this->format('u');
    }

    /**
     * @return MongoTimestamp
     */
    public function getMongoTimestamp()
    {
        if (null === $this->inc) {
            return new MongoTimestamp($this->getTimestamp());
        } else {
            return new MongoTimestamp($this->getTimestamp(), $this->inc);
        }
    }

    /**
     * @return MongoDate
     */
    public function getMongoDate()
    {
        if (0 == $this->getUsec()) {
            return new MongoDate($this->getTimestamp());
        } else {
            return new MongoDate($this->getTimestamp(), $this->getUsec());
        }
    }

    /**
     * @param MongoTimestamp $mongoTimestamp
     * @return static|DateTimestamp
     */
    public static function createFromMongoTimestamp(MongoTimestamp $mongoTimestamp)
    {
        $dateTimestamp = static::createFromTimestamp($mongoTimestamp->sec);
        $dateTimestamp->inc = $mongoTimestamp->inc;

        return $dateTimestamp;
    }

    /**
     * @param MongoDate $mongoDate
     * @return DateTimestamp
     */
    public static function createFromMongoDate(MongoDate $mongoDate)
    {
        return static::createFromTimestamp($mongoDate->sec, $mongoDate->usec);
    }

    /**
     * @param int $timestamp
     * @param int $usec
     * @return static|DateTimestamp
     */
    public static function createFromTimestamp($timestamp, $usec = null)
    {
        if (null !== $usec) {
            return static::createFromFormat('U.u', $timestamp . '.' . $usec);
        } else {
            return static::createFromFormat('U', $timestamp);
        }
    }
}
