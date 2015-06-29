<?php

namespace Lighthouse\ReportsBundle\Reports\GrossMarginSales\Receipt;

use Lighthouse\CoreBundle\Document\StockMovement\Receipt;
use Lighthouse\CoreBundle\Document\TrialBalance\TrialBalance;
use Lighthouse\CoreBundle\Types\Numeric\NumericFactory;
use Lighthouse\ReportsBundle\Reports\GrossMarginSales\GrossMarginSalesReport;

class GrossMarginSalesByReceipt extends GrossMarginSalesReport
{
    /**
     * @var Receipt
     */
    protected $receipt;

    /**
     * @param NumericFactory $numericFactory
     * @param Receipt $receipt
     */
    public function __construct(NumericFactory $numericFactory, Receipt $receipt)
    {
        $this->setEmptyValues($numericFactory);
        $this->receipt = $receipt;
    }

    /**
     * @return Receipt
     */
    public function getItem()
    {
        return $this->receipt;
    }

    /**
     * @param TrialBalance $trialBalance
     */
    public function addTrialBalanceValues(TrialBalance $trialBalance)
    {
        $this->costOfGoods = $this->costOfGoods->add($trialBalance->costOfGoods);
        $this->grossSales = $this->grossSales->add($trialBalance->totalPrice);
        $this->grossMargin = $this->grossMargin->add($trialBalance->getGrossMargin());
    }
}
