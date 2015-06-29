<?php

namespace Lighthouse\CoreBundle\Document\FirstStart;

use Lighthouse\CoreBundle\Document\AbstractDocument;
use Lighthouse\CoreBundle\Document\StockMovement\Sale\Sale;
use Lighthouse\CoreBundle\Document\Store\Store;
use Lighthouse\CoreBundle\Types\Numeric\Money;
use Lighthouse\ReportsBundle\Reports\GrossMarginSales\Receipt\GrossMarginSalesByReceipt;

/**
 * @property Store $store
 * @property Money $costOfInventory
 * @property Sale  $sale
 */
class StoreFirstStart extends AbstractDocument
{
    /**
     * @var Store
     */
    protected $store;

    /**
     * @var Money
     */
    protected $costOfInventory;

    /**
     * @var GrossMarginSalesByReceipt
     */
    protected $sale;

    /**
     * @param Store $store
     */
    public function __construct(Store $store)
    {
        $this->store = $store;
    }
}
