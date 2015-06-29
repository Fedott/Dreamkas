<?php

namespace Lighthouse\ReportsBundle\Reports\GrossMarginSales\Receipt;

use Lighthouse\CoreBundle\Document\StockMovement\Receipt;
use Lighthouse\CoreBundle\Document\TrialBalance\TrialBalanceRepository;
use Lighthouse\CoreBundle\Types\Numeric\NumericFactory;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("lighthouse.reports.gross_margin_sales.receipt.manager")
 */
class ReceiptGrossMarginSalesManager
{
    /**
     * @var NumericFactory
     */
    protected $numericFactory;

    /**
     * @var TrialBalanceRepository
     */
    protected $trialBalanceRepository;

    /**
     * @DI\InjectParams({
     *      "numericFactory" = @DI\Inject("lighthouse.core.types.numeric.factory"),
     *      "trialBalanceRepository" = @DI\Inject("lighthouse.core.document.repository.trial_balance")
     * })
     * @param NumericFactory $numericFactory
     * @param TrialBalanceRepository $trialBalanceRepository
     */
    public function __construct(NumericFactory $numericFactory, TrialBalanceRepository $trialBalanceRepository)
    {
        $this->numericFactory = $numericFactory;
        $this->trialBalanceRepository = $trialBalanceRepository;
    }

    /**
     * @param Receipt $receipt
     * @return GrossMarginSalesByReceipt
     */
    public function getReceiptReport(Receipt $receipt)
    {
        $report = new GrossMarginSalesByReceipt($this->numericFactory, $receipt);

        $trialBalances = $this->trialBalanceRepository->findByStockMovementProducts($receipt->products);

        foreach ($trialBalances as $trialBalance) {
            $report->addTrialBalanceValues($trialBalance);
        }

        return $report;
    }
}
