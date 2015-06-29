<?php

namespace Lighthouse\CoreBundle\Tests\MongoDB\Types;

use Lighthouse\CoreBundle\MongoDB\Types\DateTimeTZType;
use Lighthouse\CoreBundle\Test\TestCase;
use Doctrine\ODM\MongoDB\Types\Type;
use DateTime;
use MongoDate;

class DateTimeTZTypeTest extends TestCase
{
    /**
     * @var DateTimeTZType
     */
    protected $type;

    protected function setUp()
    {
        Type::registerType(DateTimeTZType::NAME, DateTimeTZType::getClassName());
        $this->type = Type::getType(DateTimeTZType::NAME);
    }

    /**
     * @param string $date
     * @param array $expected
     * @dataProvider convertToMongoProvider
     */
    public function testConvertToMongo($date, $expected)
    {
        $dbValue = $this->type->convertToDatabaseValue($date);
        $this->assertEquals($expected, $dbValue);
    }

    /**
     * @return array
     */
    public function convertToMongoProvider()
    {
        return array(
            '2013-01-23T23:59:59+04:00' => array(
                new DateTime('2013-01-23T23:59:59+04:00'),
                array(
                    'date' => new MongoDate(1358971199),
                    'year' => 2013,
                    'month' => 1,
                    'day' => 23,
                    'hour' => 23,
                    'minute' => 59,
                    'second' => 59,
                    'tz' => '+04:00',
                    'offset' => 14400,
                    'iso' => '2013-01-23T23:59:59+0400',
                    'dayDate' => new MongoDate(1358899200),
                    'hourDate' => new MongoDate(1358982000)
                ),
            ),
            '2013-01-23T23:59:59' => array(
                new DateTime('2013-01-23T23:59:59'),
                array(
                    'date' => new MongoDate(1358971199),
                    'year' => 2013,
                    'month' => 1,
                    'day' => 23,
                    'hour' => 23,
                    'minute' => 59,
                    'second' => 59,
                    'tz' => 'Europe/Moscow',
                    'offset' => 14400,
                    'iso' => '2013-01-23T23:59:59+0400',
                    'dayDate' => new MongoDate(1358899200),
                    'hourDate' => new MongoDate(1358982000)
                ),
            ),
            'null' => array(
                null,
                null
            ),
            'invalid int' => array(
                1358971199,
                null
            ),
        );
    }

    /**
     * @param $value
     * @param $expected
     * @dataProvider convertToPhpProvider
     */
    public function testConvertToPhp($value, $expected)
    {
        $phpValue = $this->type->convertToPHPValue($value);
        $this->assertEquals($expected, $phpValue);
    }

    /**
     * @return array
     */
    public function convertToPhpProvider()
    {
        return array(
            '2013-01-23T23:59:59+04:00' => array(
                array(
                    'date' => new MongoDate(1358971199),
                    'year' => 2013,
                    'month' => 1,
                    'day' => 23,
                    'hour' => 23,
                    'minute' => 59,
                    'second' => 59,
                    'tz' => '+04:00',
                    'offset' => 14400,
                    'iso' => '2013-01-23T23:59:59+0400'
                ),
                new DateTime('2013-01-23T23:59:59+0400'),
            ),
            '2013-01-23T23:59:59' => array(
                array(
                    'date' => new MongoDate(1358971199),
                    'year' => 2013,
                    'month' => 1,
                    'day' => 23,
                    'hour' => 23,
                    'minute' => 59,
                    'second' => 59,
                    'tz' => 'Europe/Moscow',
                    'offset' => 14400,
                    'iso' => '2013-01-23T23:59:59+0400'
                ),
                new DateTime('2013-01-23T23:59:59+0400'),
            ),
            'null' => array(
                null,
                null
            ),
            'invalid int' => array(
                1358971199,
                null
            ),
            'invalid array' => array(
                array(),
                null
            ),
            'invalid MongoDate' => array(
                new MongoDate(),
                null
            )
        );
    }

    public function testClosureToMongo()
    {
        $this->assertEquals(
            '$return = \Lighthouse\CoreBundle\MongoDB\Types\DateTimeTZType::convertToMongo($value);',
            $this->type->closureToMongo()
        );
    }

    public function testClosureToPHP()
    {
        $this->assertEquals(
            '$return = \Lighthouse\CoreBundle\MongoDB\Types\DateTimeTZType::convertToPhp($value);',
            $this->type->closureToPHP()
        );
    }
}
