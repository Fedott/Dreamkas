<?php

namespace Lighthouse\ReportsBundle\Document\GrossReturn\Network;

use DateTime;
use JMS\Serializer\Annotation\Exclude;
use Lighthouse\CoreBundle\Document\AbstractDocument;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Lighthouse\CoreBundle\Document\CashFlow\CashFlow;
use Lighthouse\CoreBundle\Document\CashFlow\CashFlowable;
use Lighthouse\CoreBundle\Types\Numeric\Money;
use Lighthouse\CoreBundle\Types\Numeric\Quantity;

/**
 * @property string $id
 * @property DateTime $day
 * @property Money $grossReturn
 * @property Quantity $quantity
 *
 * @MongoDB\Document(
 *      repositoryClass="Lighthouse\ReportsBundle\Document\GrossReturn\Network\GrossReturnNetworkRepository"
 * )
 * @MongoDB\Index(keys={"day"="asc"})
 */
class GrossReturnNetwork extends AbstractDocument implements CashFlowable
{
    /**
     * @MongoDB\Id(strategy="NONE")
     * @Exclude
     * @var string
     */
    protected $id;

    /**
     * @MongoDB\Date
     * @var DateTime
     */
    protected $day;

    /**
     * @MongoDB\Field(type="money")
     * @var Money
     */
    protected $grossReturn;

    /**
     * @MongoDB\Field(type="quantity")
     * @var Quantity
     */
    protected $quantity;

    /**
     * @return bool
     */
    public function cashFlowNeeded()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getCashFlowReasonType()
    {
        return 'Returns';
    }

    /**
     * @return Money
     */
    public function getCashFlowAmount()
    {
        return $this->grossReturn;
    }

    /**
     * @return string
     */
    public function getCashFlowDirection()
    {
        return CashFlow::DIRECTION_OUT;
    }

    /**
     * @return DateTime;
     */
    public function getCashFlowDate()
    {
        $date = clone $this->day;
        return $date->setTime(23, 59, 59);
    }

    /**
     * @return DateTime
     */
    public function getCashFlowReasonDate()
    {
        return $this->day;
    }
}
