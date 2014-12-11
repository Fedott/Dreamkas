<?php

namespace Lighthouse\CoreBundle\Test\Client\Request;

use DateTime;

class StockMovementBuilder
{
    /**
     * @var array
     */
    protected $data = array(
        'date' => null,
        'products' => array(),
    );

    /**
     * @param string $date
     * @param string $storeId
     */
    public function __construct($date = null, $storeId = null)
    {
        $this->setDate($date);
        if ($storeId) {
            $this->setStore($storeId);
        }
    }

    /**
     * @param string $date
     * @return $this
     */
    public function setDate($date = null)
    {
        $this->data['date'] = null !== $date ? $date : date(DateTime::W3C);
        return $this;
    }

    /**
     * @param string $storeId
     * @return $this
     */
    public function setStore($storeId)
    {
        $this->data['store'] = $storeId;
        return $this;
    }

    /**
     * @param string $productId
     * @param float $quantity
     * @param float $price
     * @return $this
     */
    public function addProduct($productId, $quantity = 1.0, $price = 5.99)
    {
        $this->data['products'][] = array(
            'product' => $productId,
            'quantity' => $quantity,
            'price' => $price
        );
        return $this;
    }

    /**
     * @param int $index
     * @param string $productId
     * @param float $quantity
     * @param float $price
     * @return $this
     */
    public function setProduct($index, $productId, $quantity = 1.0, $price = 5.99)
    {
        $this->data['products'][$index] = array(
            'product' => $productId,
            'quantity' => $quantity,
            'price' => $price
        );
        return $this;
    }

    /**
     * @param int $index
     * @param array $data
     * @return $this
     */
    public function mergeProduct($index, array $data)
    {
        $this->data['products'][$index] = $data + $this->data['products'][$index];
        return $this;
    }

    /**
     * @param int $index
     * @return $this
     */
    public function removeProduct($index)
    {
        unset($this->data['products'][$index]);
        return $this;
    }

    /**
     * @param array $mergeData
     * @return array
     */
    public function toArray($mergeData = array())
    {
        return $mergeData + $this->data;
    }

    /**
     * @param string $date
     * @param string $storeId
     * @return static
     */
    public static function create($date = null, $storeId = null)
    {
        return new static($date, $storeId);
    }
}
