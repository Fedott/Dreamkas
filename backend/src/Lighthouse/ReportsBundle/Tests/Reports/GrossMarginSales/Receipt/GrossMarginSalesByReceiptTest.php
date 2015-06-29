<?php

namespace Lighthouse\ReportsBundle\Tests\Reports\GrossMarginSales\Receipt;

use Lighthouse\CoreBundle\Document\StockMovement\ReceiptRepository;
use Lighthouse\CoreBundle\Test\ContainerAwareTestCase;
use Lighthouse\ReportsBundle\Reports\GrossMargin\GrossMarginManager;
use Lighthouse\ReportsBundle\Reports\GrossMarginSales\Receipt\ReceiptGrossMarginSalesManager;

class GrossMarginSalesByReceiptTest extends ContainerAwareTestCase
{
    public function testReportBySale()
    {
        $this->clearMongoDb();
        $this->authenticateProject();

        $store = $this->factory()->store()->getStore();
        $products = $this->factory()->catalog()->getProductByNames(array('1', '2', '3'));

        $this->factory()
            ->invoice()
                ->createInvoice(array('date' => '-1 day'), $store->id)
                ->createInvoiceProduct($products['1']->id, 10, 50)
                ->createInvoiceProduct($products['2']->id, 20, 40)
                ->createInvoiceProduct($products['3']->id, 30, 30)
            ->flush();

        $recalculatedSale = $this->factory()
            ->receipt()
                ->createSale($store)
                ->createReceiptProduct($products['1']->id, 2, 69.99)
                ->createReceiptProduct($products['2']->id, 5.678, 44.49)
            ->flush();

        $trialBalanceCount = $this->getGrossMarginManager()->calculateGrossMarginUnprocessedTrialBalance();
        $this->assertEquals(3, $trialBalanceCount);

        $recalculatedSale = $this->getReceiptRepository()->find($recalculatedSale->id);

        $report = $this->getReceiptReportManager()->getReceiptReport($recalculatedSale);

        $this->assertSame('392.59', $report->grossSales->toString());
        $this->assertSame('327.12', $report->costOfGoods->toString());
        $this->assertSame('65.47', $report->grossMargin->toString());

        $this->assertSame($recalculatedSale, $report->getItem());
    }

    /**
     * @return GrossMarginManager
     */
    protected function getGrossMarginManager()
    {
        return $this->getContainer()->get('lighthouse.reports.gross_margin.manager');
    }

    /**
     * @return ReceiptGrossMarginSalesManager
     */
    protected function getReceiptReportManager()
    {
        return $this->getContainer()->get('lighthouse.reports.gross_margin_sales.receipt.manager');
    }

    /**
     * @return ReceiptRepository
     */
    protected function getReceiptRepository()
    {
        return $this->getContainer()->get('lighthouse.core.document.repository.receipt');
    }
}
