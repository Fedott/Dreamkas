<?php

namespace Lighthouse\CoreBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as Serializer;
use Lighthouse\CoreBundle\Types\Money;

/**
 * Сальдовая ведомость
 *
 * @property string $id
 * @property float  $beginningBalance
 * @property Money  $beginningBalanceMoney
 * @property float  $endingBalance
 * @property Money  $endingBalanceMoney
 * @property float  $quantity
 * @property Money  $totalPrice
 * @property Money  $price
 *
 * @MongoDB\Document(
 *     repositoryClass="Lighthouse\CoreBundle\Document\TrialBalanceRepository"
 * )
 *
 * @package Lighthouse\CoreBundle\Document
 */
class TrialBalance extends AbstractDocument
{
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
     * Количество
     * @MongoDB\Int
     * @var float
     */
    protected $quantity;

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
     * @MongoDB\Date
     * @var \DateTime
     */
    protected $createdDate;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Product", simple=true, cascade={"persist"})
     * @var Product
     */
    protected $product;

    /**
     * Основание
     * @MongoDB\ReferenceOne(
     *      discriminatorField="reasonType",
     *      discriminatorMap={
     *          "invoiceProduct"="Lighthouse\CoreBundle\Document\InvoiceProduct"
     *      }
     * )
     * @var Invoice
     */
    protected $reason;

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->id,
            'beginningBalance' => $this->beginningBalance,
            'beginningBalanceMoney' => $this->beginningBalanceMoney,
            'endingBalance' => $this->endingBalance,
            'endingBalanceMoney' => $this->endingBalanceMoney,
            'quantity' => $this->quantity,
            'totalPrice' => $this->totalPrice,
            'price' => $this->price,
            'createdDate' => $this->createdDate,
            'product' => $this->product,
            'reason' => $this->reason,
        );

    }

    /**
     * @MongoDB\PrePersist
     * @MongoDB\PreUpdate
     */
    public function updateTotalPrice()
    {
        $this->totalPrice = new Money();
        $this->totalPrice->setCountByQuantity($this->price, $this->quantity);
    }
}
