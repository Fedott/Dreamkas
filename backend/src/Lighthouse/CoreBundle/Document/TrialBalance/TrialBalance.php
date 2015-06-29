<?php

namespace Lighthouse\CoreBundle\Document\TrialBalance;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as Serializer;
use Lighthouse\CoreBundle\Document\AbstractDocument;
use Lighthouse\CoreBundle\Document\Classifier\SubCategory\SubCategory;
use Lighthouse\CoreBundle\Document\Product\Product;
use Lighthouse\CoreBundle\Document\Product\Store\StoreProduct;
use Lighthouse\CoreBundle\Document\StockMovement\StockMovementProduct;
use Lighthouse\CoreBundle\Document\Store\Store;
use Lighthouse\CoreBundle\Types\Numeric\Money;
use Lighthouse\CoreBundle\Types\Numeric\Quantity;
use DateTime;

/**
 * Сальдовая ведомость
 *
 * @property string         $id
 * @property float          $beginningBalance
 * @property Money          $beginningBalanceMoney
 * @property float          $endingBalance
 * @property Money          $endingBalanceMoney
 * @property Quantity       $startIndex
 * @property Quantity       $endIndex
 * @property int            $processingStatus
 * @property Quantity       $quantity
 * @property Quantity       $inventory
 * @property Money          $price
 * @property Money          $totalPrice
 * @property Money          $costOfGoods
 * @property DateTime       $createdDate
 * @property StoreProduct   $storeProduct
 * @property Product        $product
 * @property Store          $store
 * @property StockMovementProduct     $reason
 *
 * @MongoDB\Document(
 *     repositoryClass="Lighthouse\CoreBundle\Document\TrialBalance\TrialBalanceRepository"
 * )
 * @MongoDB\HasLifecycleCallbacks
 *
 * @MongoDB\Indexes({
 *      @MongoDB\Index(keys={
 *          "reason.$ref"="asc",
 *          "createdDate.date"="asc",
 *          "storeProduct"="asc"
 *      })
 * })
 */
class TrialBalance extends AbstractDocument
{
    const PROCESSING_STATUS_OK = 0;
    const PROCESSING_STATUS_UNPROCESSED = 1;

    /**
     * @MongoDB\Id
     * @var string
     */
    protected $id;

    /**
     * Начальное сальдо
     * @MongoDB\Float
     * @var float
     */
    protected $beginningBalance;

    /**
     * @MongoDB\Field(type="money")
     * @var Money
     */
    protected $beginningBalanceMoney;

    /**
     * Конечное сальдо
     * @MongoDB\Float
     * @var float
     */
    protected $endingBalance;

    /**
     * @MongoDB\Field(type="money")
     * @var Money
     */
    protected $endingBalanceMoney;

    /**
     * @MongoDB\Field(type="quantity")
     * @var Quantity
     */
    protected $startIndex;

    /**
     * @MongoDB\Field(type="quantity")
     * @var Quantity
     */
    protected $endIndex;

    /**
     * @MongoDB\Float
     * @MongoDB\Index
     * @var integer
     */
    protected $processingStatus = self::PROCESSING_STATUS_UNPROCESSED;

    /**
     * Количество
     * @MongoDB\Field(type="quantity")
     * @var float
     */
    protected $quantity;

    /**
     * Количество непроданных единиц (для приемок)
     * @MongoDB\Field(type="quantity")
     * @var float
     */
    protected $inventory;

    /**
     * @MongoDB\Field(type="money")
     * @var Money
     */
    protected $costOfGoods;

    /**
     * @MongoDB\Field(type="money")
     * @var Money
     */
    protected $totalPrice;

    /**
     * Стоимость единицы товара
     * @MongoDB\Field(type="money")
     * @var Money
     */
    protected $price;

    /**
     * @MongoDB\Field(type="datetime_tz")
     * @var DateTime
     */
    protected $createdDate;

    /**
     * @MongoDB\ReferenceOne(
     *     targetDocument="Lighthouse\CoreBundle\Document\Product\Store\StoreProduct",
     *     simple=true,
     *     cascade={"persist"}
     * )
     * @var StoreProduct
     */
    protected $storeProduct;

    /**
     * @MongoDB\ReferenceOne(
     *     targetDocument="Lighthouse\CoreBundle\Document\Product\Product",
     *     simple=true,
     *     cascade={"persist"}
     * )
     * @var Product
     */
    protected $product;

    /**
     * @MongoDB\ReferenceOne(
     *     targetDocument="Lighthouse\CoreBundle\Document\Store\Store",
     *     simple=true,
     *     cascade={"persist"}
     * )
     * @var Store
     */
    protected $store;

    /**
     * @MongoDB\ReferenceOne(
     *     targetDocument="Lighthouse\CoreBundle\Document\Classifier\SubCategory\SubCategory",
     *     simple=true,
     *     cascade={"persist"}
     * )
     * @var SubCategory
     */
    protected $subCategory;

    /**
     * Основание
     * @MongoDB\ReferenceOne(
     *      discriminatorField="reasonType",
     *      discriminatorMap={
     *          "invoiceProduct"="Lighthouse\CoreBundle\Document\StockMovement\Invoice\InvoiceProduct",
     *          "saleProduct"="Lighthouse\CoreBundle\Document\Sale\SaleProduct",
     *          "returnProduct"="Lighthouse\CoreBundle\Document\Returne\ReturnProduct",
     *          "writeOffProduct"="Lighthouse\CoreBundle\Document\WriteOff\WriteOffProduct"
     *      }
     * )
     * @var StockMovementProduct
     */
    protected $reason;

    /**
     *
     */
    public function __construct()
    {
        $this->createdDate = new DateTime;
    }

    /**
     * @MongoDB\PrePersist
     * @MongoDB\PreUpdate
     */
    public function updateTotalPrice()
    {
        $this->totalPrice = $this->price->mul($this->quantity);
        $this->store = $this->storeProduct->store;
        $this->product = $this->storeProduct->product;
        $this->subCategory = $this->reason->product->subCategory;
    }

    /**
     * @return Money|null
     */
    public function getGrossMargin()
    {
        if ($this->costOfGoods && $this->totalPrice) {
            return $this->totalPrice->sub($this->costOfGoods);
        } else {
            return null;
        }
    }
}
