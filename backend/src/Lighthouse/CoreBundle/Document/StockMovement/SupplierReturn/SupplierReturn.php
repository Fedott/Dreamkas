<?php

namespace Lighthouse\CoreBundle\Document\StockMovement\SupplierReturn;

use Lighthouse\CoreBundle\Document\StockMovement\StockMovement;
use Lighthouse\CoreBundle\Document\Supplier\Supplier;
use Lighthouse\CoreBundle\MongoDB\Generated\Generated;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Lighthouse\CoreBundle\Validator\Constraints as AssertLH;

/**
 * @property string $number
 * @property bool   $paid
 * @property Supplier $supplier
 * @property SupplierReturnProduct[]|Collection $products
 *
 * @MongoDB\Document(
 *      repositoryClass="Lighthouse\CoreBundle\Document\StockMovement\SupplierReturn\SupplierReturnRepository"
 * )
 */
class SupplierReturn extends StockMovement
{
    const TYPE = 'SupplierReturn';

    /**
     * @MongoDB\ReferenceMany(
     *      targetDocument="Lighthouse\CoreBundle\Document\StockMovement\SupplierReturn\SupplierReturnProduct",
     *      simple=true,
     *      cascade={"persist","remove"},
     *      mappedBy="parent"
     * )
     * @Serializer\MaxDepth(4)
     * @var SupplierReturnProduct[]|Collection
     */
    protected $products;

    /**
     * @Generated(startValue=10000)
     * @var int
     */
    protected $number;

    /**
     * Поставщик
     * @MongoDB\ReferenceOne(
     *     targetDocument="Lighthouse\CoreBundle\Document\Supplier\Supplier",
     *     simple=true
     * )
     * @AssertLH\Reference(message="lighthouse.validation.errors.invoice.supplier.does_not_exists")
     * @AssertLH\NotDeleted(true, isDeletedMessage="lighthouse.validation.errors.deleted.supplier.forbid.edit")
     * @var Supplier
     */
    protected $supplier;

    /**
     * @MongoDB\Boolean
     * @var bool
     */
    protected $paid;
}
