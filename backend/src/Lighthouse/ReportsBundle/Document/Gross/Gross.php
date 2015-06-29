<?php

namespace Lighthouse\ReportsBundle\Document\Gross;

use Lighthouse\CoreBundle\Document\AbstractDocument;
use JMS\Serializer\Annotation as Serializer;
use Lighthouse\CoreBundle\Types\Numeric\Money;
use Lighthouse\CoreBundle\Types\Numeric\Quantity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @property string         $id
 * @property Money          $grossSales
 * @property Money          $grossMargin
 * @property Money          $costOfGoods
 * @property Quantity       $quantity
 *
 * @MongoDB\MappedSuperclass
 * @MongoDB\InheritanceType("COLLECTION_PER_CLASS")
 */
abstract class Gross extends AbstractDocument
{
    /**
     * @MongoDB\Id(strategy="NONE")
     * @Serializer\Exclude
     * @var string
     */
    protected $id;

    /**
     * @MongoDB\Field(type="money")
     * @var Money
     */
    protected $grossSales;

    /**
     * @MongoDB\Field(type="money")
     * @var Money
     */
    protected $grossMargin;

    /**
     * @MongoDB\Field(type="money")
     * @var Money
     */
    protected $costOfGoods;

    /**
     * @MongoDB\Field(type="quantity")
     * @var Quantity
     */
    protected $quantity;
}
