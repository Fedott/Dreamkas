<?php

namespace Lighthouse\ReportsBundle\Document\Gross\Hour;

use Lighthouse\ReportsBundle\Document\Gross\Gross;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use DateTime;

/**
 * @property DateTime $hourDate
 */
class HourGross extends Gross
{
    /**
     * @MongoDB\Field(type="DateTimeUTC")
     * @var DateTime
     */
    protected $hourDate;
}
