<?php

namespace Lighthouse\ReportsBundle\Document\CostOfInventory\Store;

use JMS\Serializer\Annotation as Serialize;
use Lighthouse\CoreBundle\Document\AbstractDocument;
use Lighthouse\CoreBundle\Document\Store\Store;
use Lighthouse\CoreBundle\Types\Numeric\Money;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @property string $id
 * @property Store  $store
 * @property Money  $costOfInventory
 * @property bool   $needRecalculate
 *
 * @MongoDB\Document(
 *      repositoryClass="Lighthouse\ReportsBundle\Document\CostOfInventory\Store\StoreCostOfInventoryRepository"
 * )
 */
class StoreCostOfInventory extends AbstractDocument
{
    /**
     * @MongoDB\Id(strategy="NONE")
     * @Serialize\Exclude
     * @var string
     */
    protected $id;

    /**
     * @MongoDB\ReferenceOne(
     *     targetDocument="Lighthouse\CoreBundle\Document\Store\Store",
     *     simple=true
     * )
     * @var Store
     */
    protected $store;

    /**
     * @MongoDB\Field(type="money")
     * @var Money
     */
    protected $costOfInventory;

    /**
     * @MongoDB\Boolean
     * @Serialize\Exclude
     * @var bool
     */
    protected $needRecalculate = false;
}
