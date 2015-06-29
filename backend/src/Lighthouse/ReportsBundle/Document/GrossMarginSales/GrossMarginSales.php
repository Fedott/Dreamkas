<?php

namespace Lighthouse\ReportsBundle\Document\GrossMarginSales;

use Lighthouse\CoreBundle\Document\AbstractDocument;
use Lighthouse\CoreBundle\Document\Store\Store;
use Lighthouse\CoreBundle\Types\Numeric\Money;
use Lighthouse\CoreBundle\Types\Numeric\Quantity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation\Exclude;
use DateTime;
use Lighthouse\ReportsBundle\Document\Gross\Gross;

/**
 * @property DateTime       $day
 * @property Store          $store
 *
 * @MongoDB\MappedSuperclass
 * @MongoDB\InheritanceType("COLLECTION_PER_CLASS")
 */
abstract class GrossMarginSales extends Gross
{
    /**
     * @MongoDB\Date
     * @var \DateTime
     */
    protected $day;

    /**
     * @MongoDB\ReferenceOne(
     *     targetDocument="Lighthouse\CoreBundle\Document\Store\Store",
     *     simple=true
     * )
     * @var Store
     */
    protected $store;

    /**
     * @return object
     */
    abstract public function getItem();
}
