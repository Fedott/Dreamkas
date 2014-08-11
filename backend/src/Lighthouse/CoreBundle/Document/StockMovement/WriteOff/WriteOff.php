<?php

namespace Lighthouse\CoreBundle\Document\StockMovement\WriteOff;


use Lighthouse\CoreBundle\Document\StockMovement\StockMovement;
use Lighthouse\CoreBundle\Document\StockMovement\WriteOff\Product\WriteOffProduct;
use Lighthouse\CoreBundle\MongoDB\Generated\Generated;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property string $number
 * @property WriteOffProduct[]|Collection $products
 *
 * @MongoDB\Document(repositoryClass="Lighthouse\CoreBundle\Document\StockMovement\WriteOff\WriteOffRepository")
 */
class WriteOff extends StockMovement
{
    const TYPE = 'WriteOff';

    /**
     * @Generated(startValue=10000)
     * @var int
     */
    protected $number;

    /**
     * @MongoDB\ReferenceMany(
     *      targetDocument="Lighthouse\CoreBundle\Document\StockMovement\WriteOff\Product\WriteOffProduct",
     *      simple=true,
     *      cascade={"persist","remove"},
     *      mappedBy="writeOff"
     * )
     * @Assert\Valid(traverse=true)
     * @Assert\Count(
     *      min=1,
     *      minMessage="lighthouse.validation.errors.writeoff.products.empty"
     * )
     * @var WriteOffProduct[]|Collection
     */
    protected $products;
}
