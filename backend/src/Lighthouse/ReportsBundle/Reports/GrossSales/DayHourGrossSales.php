<?php

namespace Lighthouse\ReportsBundle\Reports\GrossSales;

use Lighthouse\CoreBundle\Document\AbstractDocument;
use Lighthouse\CoreBundle\Types\Numeric\Money;
use DateTime;

class DayHourGrossSales extends AbstractDocument
{
    /**
     * @var DateTime
     */
    protected $dayHour;

    /**
     * @var Money
     */
    protected $runningSum;

    /**
     * @param DateTime $dayHour
     */
    public function __construct(DateTime $dayHour)
    {
        $this->dayHour = $dayHour;
        $this->runningSum = new Money(0);
    }

    /**
     * @param Money $runningSum
     */
    public function addRunningSum(Money $runningSum)
    {
        $this->runningSum = $this->runningSum->add($runningSum);
    }
}
