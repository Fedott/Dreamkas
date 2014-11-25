<?php

namespace Lighthouse\CoreBundle\Test\Client\Request;

class SupplierReturnBuilder extends StockMovementBuilder
{
    /**
     * @var array
     */
    protected $data = array(
        'date' => null,
        'paid' => false,
        'supplier' => null,
        'products' => array(),
    );

    /**
     * @param string $storeId
     * @param string $date
     * @param string $supplierId
     * @param bool $paid
     */
    public function __construct($storeId = null, $date = null, $supplierId = null, $paid = false)
    {
        parent::__construct($date, $storeId);

        if ($supplierId) {
            $this->setSupplierId($supplierId);
        }
        if ($paid) {
            $this->setPaid($paid);
        }
    }

    /**
     * @param string $supplierId
     */
    public function setSupplierId($supplierId)
    {
        $this->data['supplier'] = $supplierId;
    }

    /**
     * @param bool $paid
     */
    public function setPaid($paid)
    {
        $this->data['paid'] = $paid;
    }

    /**
     * @param string $storeId
     * @param string $date
     * @param string $supplierId
     * @param bool $paid
     * @return SupplierReturnBuilder
     */
    public static function create($storeId = null, $date = null, $supplierId = null, $paid = false)
    {
        return new self($storeId, $date, $supplierId, $paid);
    }
}
