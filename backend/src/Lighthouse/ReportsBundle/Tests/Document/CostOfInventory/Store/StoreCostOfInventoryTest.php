<?php

namespace Lighthouse\ReportsBundle\Tests\Document\CostOfInventory\Store;

use Lighthouse\CoreBundle\Document\Store\Store;
use Lighthouse\IntegrationBundle\Test\ContainerAwareTestCase;
use Lighthouse\ReportsBundle\Document\CostOfInventory\Store\StoreCostOfInventory;
use Lighthouse\ReportsBundle\Document\CostOfInventory\Store\StoreCostOfInventoryRepository;
use Lighthouse\ReportsBundle\Reports\GrossMargin\GrossMarginManager;

class StoreCostOfInventoryTest extends ContainerAwareTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->clearJobs();
        $this->clearMongoDb();
    }

    public function testCalculate()
    {
        $this->authenticateProject();

        $stores = $this->factory()->store()->getStores(array('1', '2', '3'));
        $products = $this->factory()->catalog()->getProductByNames(array('1', '2', '3'));

        $this->factory()
            ->invoice()
                ->createInvoice(array('date' => '-3 days'), $stores['1']->id)
                ->createInvoiceProduct($products['1']->id, 10, 50)
                ->createInvoiceProduct($products['2']->id, 20, 40)
                ->createInvoiceProduct($products['3']->id, 30, 30)
            ->persist()
                ->createInvoice(array('date' => '-2 days'), $stores['2']->id)
                ->createInvoiceProduct($products['1']->id, 15, 55)
                ->createInvoiceProduct($products['2']->id, 25, 45)
                ->createInvoiceProduct($products['3']->id, 35, 35)
            ->flush();

        $this->getGrossMarginManager()->calculateGrossMarginUnprocessedTrialBalance();

        $total = $this->getStoreCostOfInventoryRepository()->recalculate();
        $this->assertEquals(2, $total);

        $this->assertStoreReport($stores['1']->id, '2200.00');
        $this->assertStoreReport($stores['2']->id, '3175.00');

        $this->factory()
            ->receipt()
                ->createSale($stores['1'])
                ->createReceiptProduct($products['1']->id, 2, 69.99)
                ->createReceiptProduct($products['2']->id, 5.678, 44.49)
            ->persist()
                ->createSale($stores['2'])
                ->createReceiptProduct($products['2']->id, 5, 49.99)
                ->createReceiptProduct($products['3']->id, 2.333, 39.89)
            ->flush();

        $this->getGrossMarginManager()->calculateGrossMarginUnprocessedTrialBalance();

        $total = $this->getStoreCostOfInventoryRepository()->recalculate();
        $this->assertEquals(2, $total);

        $this->assertStoreReport($stores['1']->id, '1872.88');
        $this->assertStoreReport($stores['2']->id, '2868.34');
    }

    public function testRecalculateStoreEmpty()
    {
        $this->authenticateProject();

        $store = $this->factory()->store()->createStore();
        $storeOther = $this->factory()->store()->createStore("Other");

        $product = $this->factory()->catalog()->createProduct(array());

        $this->factory()
            ->invoice()
                ->createInvoice(array(), $storeOther->id)
                ->createInvoiceProduct($product->id, 10, 10)
            ->flush();

        $this->getGrossMarginManager()->calculateGrossMarginUnprocessedTrialBalance();

        $this->getStoreCostOfInventoryRepository()->recalculateStore($store);

        $this->assertEmptyStoreReport($store->id);
    }

    public function testRecalculateStore()
    {
        $this->authenticateProject();

        $stores = $this->factory()->store()->getStores(array('1', '2', '3'));
        $products = $this->factory()->catalog()->getProductByNames(array('1', '2', '3'));

        $this->factory()
            ->invoice()
                ->createInvoice(array('date' => '-3 days'), $stores['1']->id)
                ->createInvoiceProduct($products['1']->id, 10, 50)
                ->createInvoiceProduct($products['2']->id, 20, 40)
                ->createInvoiceProduct($products['3']->id, 30, 30)
            ->persist()
                ->createInvoice(array('date' => '-2 days'), $stores['2']->id)
                ->createInvoiceProduct($products['1']->id, 15, 55)
                ->createInvoiceProduct($products['2']->id, 25, 45)
                ->createInvoiceProduct($products['3']->id, 35, 35)
            ->flush();

        $this->getGrossMarginManager()->calculateGrossMarginUnprocessedTrialBalance();

        $total = $this->getStoreCostOfInventoryRepository()->recalculateStore($stores['1']);
        $this->assertEquals(1, $total);

        $this->assertStoreReport($stores['1']->id, '2200.00');
        $this->assertEmptyStoreReport($stores['2']->id);

        $total = $this->getStoreCostOfInventoryRepository()->recalculateStore($stores['2']);
        $this->assertEquals(1, $total);

        $this->assertStoreReport($stores['1']->id, '2200.00');
        $this->assertStoreReport($stores['2']->id, '3175.00');

        $this->factory()
            ->receipt()
                ->createSale($stores['1'])
                ->createReceiptProduct($products['1']->id, 2, 69.99)
                ->createReceiptProduct($products['2']->id, 5.678, 44.49)
            ->persist()
                ->createSale($stores['2'])
                ->createReceiptProduct($products['2']->id, 5, 49.99)
                ->createReceiptProduct($products['3']->id, 2.333, 39.89)
            ->flush();

        $this->getGrossMarginManager()->calculateGrossMarginUnprocessedTrialBalance();

        $total = $this->getStoreCostOfInventoryRepository()->recalculateStore($stores['2']);
        $this->assertEquals(1, $total);

        $this->assertStoreReport($stores['1']->id, '2200.00');
        $this->assertStoreReport($stores['2']->id, '2868.34');

        $this->getDocumentManager()->clear();
        $total = $this->getStoreCostOfInventoryRepository()->recalculateStore($stores['1']);
        $this->assertEquals(1, $total);

        $this->assertStoreReport($stores['1']->id, '1872.88');
        $this->assertStoreReport($stores['2']->id, '2868.34');
    }

    public function testRecalculateStoreAfterDeleteAllInvoices()
    {
        $this->authenticateProject();

        $store = $this->factory()->store()->createStore();
        $storeOther = $this->factory()->store()->createStore("Other");

        $product = $this->factory()->catalog()->createProduct(array());

        $invoice = $this->factory()
            ->invoice()
                ->createInvoice(array(), $store->id)
                ->createInvoiceProduct($product->id, 10, 10)
            ->flush();

        $this->getGrossMarginManager()->calculateGrossMarginUnprocessedTrialBalance();
        $this->getStoreCostOfInventoryRepository()->recalculateStore($store);

        $this->assertStoreReport($store->id, '100.00');

        $this->factory()
            ->invoice()
                ->editInvoice($invoice->id, array(), $storeOther->id)
            ->flush();

        $this->getGrossMarginManager()->calculateGrossMarginUnprocessedTrialBalance();
        $this->getStoreCostOfInventoryRepository()->recalculateStore($store);

        $this->assertEmptyStoreReport($store->id);
    }

    public function testRecalculateStoreIsNeededMethod()
    {
        $mockRepository = $this
            ->getMockBuilder('Lighthouse\ReportsBundle\Document\CostOfInventory\Store\StoreCostOfInventoryRepository')
            ->setMethods(array('find', 'recalculateStore'))
            ->disableOriginalConstructor()
            ->getMock();

        $store = new Store();
        $reportObject = new StoreCostOfInventory();
        $reportObject->needRecalculate = true;
        $reportObject->store = $store;
        $mockRepository->method('find')
            ->will($this->returnValue($reportObject));

        $mockRepository->expects($this->once())
            ->method('recalculateStore');

        $mockRepository->recalculateStoreIsNeeded('storeId');
    }

    public function testCalculateWithJobs()
    {
        $this->authenticateProject();

        $stores = $this->factory()->store()->getStores(array('1', '2', '3'));
        $products = $this->factory()->catalog()->getProductByNames(array('1', '2', '3'));

        $this->factory()
            ->invoice()
                ->createInvoice(array('date' => '-3 days'), $stores['1']->id)
                ->createInvoiceProduct($products['1']->id, 10, 50)
                ->createInvoiceProduct($products['2']->id, 20, 40)
                ->createInvoiceProduct($products['3']->id, 30, 30)
            ->persist()
                ->createInvoice(array('date' => '-2 days'), $stores['2']->id)
                ->createInvoiceProduct($products['1']->id, 15, 55)
                ->createInvoiceProduct($products['2']->id, 25, 45)
                ->createInvoiceProduct($products['3']->id, 35, 35)
            ->flush();

        $this->processedAllJobs();

        $this->assertStoreReport($stores['1']->id, '2200.00');
        $this->assertStoreReport($stores['2']->id, '3175.00');

        $this->factory()
            ->receipt()
                ->createSale($stores['1'])
                ->createReceiptProduct($products['1']->id, 2, 69.99)
                ->createReceiptProduct($products['2']->id, 5.678, 44.49)
            ->persist()
                ->createSale($stores['2'])
                ->createReceiptProduct($products['2']->id, 5, 49.99)
                ->createReceiptProduct($products['3']->id, 2.333, 39.89)
            ->flush();

        $this->processedAllJobs();

        $this->getDocumentManager()->clear();

        $this->assertStoreReport($stores['1']->id, '1872.88');
        $this->assertStoreReport($stores['2']->id, '2868.34');
    }

    /**
     * @param $storeId
     * @param $expectedCostOfInventory
     */
    public function assertStoreReport($storeId, $expectedCostOfInventory)
    {
        $storeReport = $this->getStoreCostOfInventoryRepository()->find($storeId);
        $this->assertNotNull($storeReport, 'Store report not found');
        $this->assertSame($expectedCostOfInventory, $storeReport->costOfInventory->toString());
    }

    /**
     * @param $storeId
     */
    public function assertEmptyStoreReport($storeId)
    {
        $storeReport = $this->getStoreCostOfInventoryRepository()->find($storeId);
        $this->assertNull($storeReport);
    }

    /**
     * @return GrossMarginManager
     */
    protected function getGrossMarginManager()
    {
        return $this->getContainer()->get('lighthouse.reports.gross_margin.manager');
    }

    /**
     * @return StoreCostOfInventoryRepository
     */
    protected function getStoreCostOfInventoryRepository()
    {
        return $this->getContainer()->get('lighthouse.reports.document.cost_of_inventory.store.repository');
    }
}
