<?php

namespace Lighthouse\CoreBundle\Document\StockMovement\Returne;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Lighthouse\CoreBundle\Document\StockMovement\StockMovementProduct;
use Lighthouse\CoreBundle\Types\Numeric\Money;
use Symfony\Component\Validator\Constraints as Assert;
use Lighthouse\CoreBundle\Validator\Constraints as LighthouseAssert;
use JMS\Serializer\Annotation as Serializer;

/**
 * @MongoDB\Document
 * @MongoDB\HasLifecycleCallbacks
 *
 * @property Returne $parent
 */
class ReturnProduct extends StockMovementProduct
{
    const REASON_TYPE = 'ReturnProduct';

    /**
     * Цена продажи
     * @MongoDB\Field(type="money")
     * @Assert\NotBlank(groups={"Default","products"})
     * @LighthouseAssert\Money(notBlank=true, groups={"Default","products"})
     * @var Money
     */
    protected $price;

    /**
     * @MongoDB\ReferenceOne(
     *     targetDocument="Lighthouse\CoreBundle\Document\StockMovement\Returne\Returne",
     *     simple=true,
     *     cascade="persist",
     *     inversedBy="products"
     * )
     * @Assert\NotBlank
     * @Serializer\MaxDepth(2)
     * @var Returne
     */
    protected $parent;

    /**
     * @return boolean
     */
    public function increaseAmount()
    {
        return true;
    }
}
