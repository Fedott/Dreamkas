<?php

namespace Lighthouse\CoreBundle\Document\Order;

use Lighthouse\CoreBundle\Document\AbstractDocument;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as Serializer;
use Lighthouse\CoreBundle\Document\Order\Product\OrderProduct;
use Lighthouse\CoreBundle\Document\Order\Product\OrderProductCollection;
use Lighthouse\CoreBundle\Document\ReferenceCollection;
use Lighthouse\CoreBundle\Document\Store\Store;
use Lighthouse\CoreBundle\Document\Store\Storeable;
use Lighthouse\CoreBundle\Document\Supplier\Supplier;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;

/**
 * @property string $id
 * @property Store $store
 * @property Supplier $supplier
 * @property OrderProduct[] $products
 * @property DateTime $createdDate
 *
 * @MongoDB\Document(
 *     repositoryClass="Lighthouse\CoreBundle\Document\Order\OrderRepository"
 * )
 */
class Order extends AbstractDocument implements Storeable
{
    /**
     * @MongoDB\Id
     * @var string
     */
    protected $id;

    /**
     * @MongoDB\ReferenceOne(
     *     targetDocument="Lighthouse\CoreBundle\Document\Store\Store",
     *     simple=true
     * )
     * @Assert\NotBlank
     * @Serializer\MaxDepth(2)
     * @var Store
     */
    protected $store;

    /**
     * Поставщик
     * @MongoDB\ReferenceOne(
     *     targetDocument="Lighthouse\CoreBundle\Document\Supplier\Supplier",
     *     simple=true
     * )
     * @Assert\NotBlank
     * @var Supplier
     */
    protected $supplier;

    /**
     * @MongoDB\ReferenceMany(
     *      targetDocument="Lighthouse\CoreBundle\Document\Order\Product\OrderProduct",
     *      simple=true,
     *      cascade={"persist","remove"},
     *      mappedBy="order"
     * )
     *
     * @Assert\Valid(traverse=true)
     * @Serializer\MaxDepth(4)
     * @var OrderProduct[]
     */
    protected $products;

    /**
     * Дата составления накладной
     * @MongoDB\Date
     * @var \DateTime
     */
    protected $createdDate;

    /**
     *
     */
    public function __construct()
    {
        $this->createdDate = new DateTime();
        $this->products = new ReferenceCollection($this, 'order');
    }

    /**
     * @return Store
     */
    public function getStore()
    {
        return $this->store;
    }
}
