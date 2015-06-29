<?php

namespace Lighthouse\CoreBundle\Tests\Document\TrialBalance;

use Lighthouse\CoreBundle\Document\StockMovement\Invoice\Invoice;
use Lighthouse\CoreBundle\Document\StockMovement\Invoice\InvoiceProduct;
use Lighthouse\CoreBundle\Document\Product\Store\StoreProductRepository;
use Lighthouse\CoreBundle\Document\StockMovement\Sale\SaleProduct;
use Lighthouse\CoreBundle\Document\StockMovement\StockMovementProduct;
use Lighthouse\CoreBundle\Document\TrialBalance\CostOfGoods\CostOfGoodsCalculator;
use Lighthouse\CoreBundle\Document\TrialBalance\TrialBalance;
use Lighthouse\CoreBundle\Document\TrialBalance\TrialBalanceRepository;
use Lighthouse\CoreBundle\Test\WebTestCase;
use Lighthouse\CoreBundle\Types\Numeric\NumericFactory;
use Lighthouse\CoreBundle\Types\Numeric\Quantity;

class CostOfGoodsTest extends WebTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->authenticateProject();
    }

    public function testIndexRangeCreatedOnInvoiceConsecutiveInsert()
    {
        $products = $this->factory()->catalog()->getProductByNames(array('1', '2', '3'));

        $store = $this->factory()->store()->getStore('701');

        $this->factory()
            ->invoice()
                ->createInvoice(array('date' => '2014-01-12 12:23:13'), $store->id)
                ->createInvoiceProduct($products['1']->id, 105.678, 16.36)
                ->createInvoiceProduct($products['3']->id, 320, 178.34)
            ->persist()
                ->createInvoice(array('date' => '2014-01-13 19:56:04'), $store->id)
                ->createInvoiceProduct($products['2']->id, 45.04, 189.67)
                ->createInvoiceProduct($products['3']->id, 115.12, 176.51)
            ->persist()
                ->createInvoice(array('date' => '2014-01-13 20:03:14'), $store->id)
                ->createInvoiceProduct($products['1']->id, 111.67, 201.15)
                ->createInvoiceProduct($products['3']->id, 115, 176.51)
            ->persist()
                ->createInvoice(array('date' => '2014-01-14 08:15:31'), $store->id)
                ->createInvoiceProduct($products['1']->id, 300.01, 201.15)
            ->flush();

        $this->processedAllJobs();

        foreach ($products as $product) {
            $storeProductId = $this->getStoreProductRepository()->getIdByStoreIdAndProductId($store->id, $product->id);
            $prevEndIndex = '0.000';
            $trailBalances = $this->getTrialBalanceRepository()->findByStoreProductIdAndReasonType(
                $storeProductId,
                InvoiceProduct::TYPE
            );
            foreach ($trailBalances as $trailBalance) {
                $this->assertInstanceOf(Quantity::getClassName(), $trailBalance->startIndex);
                $this->assertSame($prevEndIndex, $trailBalance->startIndex->toString());
                $prevEndIndex = $trailBalance->endIndex->toString();
                $this->assertNotSame('0.000', $prevEndIndex);
            }
        }
    }

    public function testIndexRangeCreatedOnSaleConsecutiveInsert()
    {
        $products = $this->factory()->catalog()->getProductByNames(array('1', '2', '3'));

        $store = $this->factory()->store()->getStore('701');

        $this->factory()
            ->invoice()
                ->createInvoice(array('date' => '2014-01-10 12:23:13'), $store->id)
                ->createInvoiceProduct($products['1']->id, 16.36, 10.09)
                ->createInvoiceProduct($products['2']->id, 10.067, 29.56)
                ->createInvoiceProduct($products['3']->id, 20, 30.05)
            ->flush();

        $this->factory()
            ->receipt()
                ->createSale($store, '2014-01-11 13:45:09')
                ->createReceiptProduct($products['1']->id, 9.102, 12.11)
                ->createReceiptProduct($products['3']->id, 7, 34.12)
                ->createReceiptProduct($products['3']->id, 1, 34.12)
            ->flush();

        $this->factory()
            ->receipt()
                ->createSale($store, '2014-01-12 15:45:09')
                ->createReceiptProduct($products['2']->id, 2.056, 34.99)
                ->createReceiptProduct($products['3']->id, 6, 35.15)
            ->flush();

        $this->factory()
            ->receipt()
                ->createSale($store, '2014-01-12 15:45:10', 110.23)
                ->createReceiptProduct($products['1']->id, 4.56, 11.49)
                ->createReceiptProduct($products['3']->id, 2, 35.15)
            ->flush();

        $this->processedAllJobs();

        $countAssertions = array(
            '1' => 2,
            '2' => 1,
            '3' => 4,
        );

        foreach ($products as $name => $product) {
            $storeProductId = $this->getStoreProductRepository()->getIdByStoreIdAndProductId($store->id, $product->id);
            $prevEndIndex = '0.000';
            $trailBalances = $this->getTrialBalanceRepository()->findByStoreProductIdAndReasonType(
                $storeProductId,
                SaleProduct::TYPE
            );
            $this->assertEquals($countAssertions[$name], $trailBalances->count(true));
            foreach ($trailBalances as $trailBalance) {
                $this->assertInstanceOf(Quantity::getClassName(), $trailBalance->startIndex);
                $this->assertSame($prevEndIndex, $trailBalance->startIndex->toString());
                $prevEndIndex = $trailBalance->endIndex->toString();
                $this->assertNotSame('0.000', $prevEndIndex);
            }
        }
    }

    public function testIndexRangeCreatedOnSaleInsertManyPositionsInOneSale()
    {
        $product = $this->factory()->catalog()->getProduct('1');

        $store = $this->factory()->store()->getStore('701');

        $this->factory()
            ->receipt()
                ->createSale($store, '2014-01-11 13:45:09')
                ->createReceiptProduct($product->id, 3, 34.12)
                ->createReceiptProduct($product->id, 7, 34.12)
                ->createReceiptProduct($product->id, 1, 34.12)
            ->flush();

        $this->processedAllJobs();

        $storeProductId = $this->getStoreProductRepository()->getIdByStoreIdAndProductId($store->id, $product->id);
        $prevEndIndex = '0.000';
        $trailBalances = $this->getTrialBalanceRepository()->findByStoreProductIdAndReasonType(
            $storeProductId,
            SaleProduct::TYPE
        );
        $this->assertEquals(3, $trailBalances->count(true));
        foreach ($trailBalances as $trailBalance) {
            $this->assertInstanceOf(Quantity::getClassName(), $trailBalance->startIndex);
            $this->assertSame($prevEndIndex, $trailBalance->startIndex->toString());
            $prevEndIndex = $trailBalance->endIndex->toString();
            $this->assertNotSame('0.000', $prevEndIndex);
        }
    }

    /**
     * @dataProvider findInvoiceByRangeIndexProvider
     * @param float $start
     * @param float $end
     * @param array $expectedSkus
     */
    public function testFindInvoiceByRangeIndex($start, $end, array $expectedSkus)
    {
        $product = $this->factory()->catalog()->getProduct('1');
        $store = $this->factory()->store()->getStore('701');

        $storeProductId = $this->getStoreProductRepository()->getIdByStoreIdAndProductId($store->id, $product->id);

        $this->factory()
            ->invoice()
                ->createInvoice(array('date' => '2014-01-12 12:23:12'), $store->id)
                ->createInvoiceProduct($product->id, 5, 10.09)
            ->persist()
                ->createInvoice(array('date' => '2014-01-12 13:23:12'), $store->id)
                ->createInvoiceProduct($product->id, 3, 10.09)
            ->persist()
                ->createInvoice(array('date' => '2014-01-12 14:23:12'), $store->id)
                ->createInvoiceProduct($product->id, 2, 10.09)
            ->flush();

        $this->processedAllJobs();

        $startIndex = $this->getNumericFactory()->createQuantity($start);
        $endIndex = $this->getNumericFactory()->createQuantity($end);

        $cursor = $this->getTrialBalanceRepository()->findByIndexRange(
            InvoiceProduct::TYPE,
            $storeProductId,
            $startIndex,
            $endIndex
        );
        $this->assertEquals(count($expectedSkus), $cursor->count(true));
        foreach ($expectedSkus as $expectedSku) {
            /* @var TrialBalance $trialBalance */
            $trialBalance = $cursor->getNext();
            $this->assertNotNull($trialBalance);
            /* @var Invoice $invoice */
            $invoice = $trialBalance->reason->parent;
            $this->assertEquals($expectedSku, $invoice->number);
        }
    }

    /**
     * @return array
     */
    public function findInvoiceByRangeIndexProvider()
    {
        return array(
            '0,5 - exact range #1' => array(
                0,
                5,
                array(10001)
            ),
            '5,8 - exact range #2' => array(
                5,
                8,
                array(10002)
            ),
            '8,10 - exact range #3' => array(
                8,
                10,
                array(10003),
            ),
            '0,1 - start/inside #1' => array(
                0,
                1,
                array(10001)
            ),
            '1,3 - inside #1' => array(
                1,
                3,
                array(10001)
            ),
            '0,6' => array(
                0,
                6,
                array(10001, 10002)
            ),
            '4,6' => array(
                4,
                6,
                array(10001, 10002)
            ),
            '5,6' => array(
                5,
                6,
                array(10002)
            ),
            '6,8' => array(
                6,
                8,
                array(10002)
            ),
            '10,11' => array(
                10,
                11,
                array()
            ),
            '1,9' => array(
                1,
                9,
                array(10001, 10002, 10003)
            ),
            '0,11' => array(
                1,
                9,
                array(10001, 10002, 10003)
            ),
            '11,14' => array(
                11,
                14,
                array()
            )
        );
    }

    /**
     * @dataProvider costOfGoodsCalculateByIndexRangeProvider
     * @param float $start
     * @param float $end
     * @param string $expectedCostOfGoods
     */
    public function testCostOfGoodsCalculateByIndexRange($start, $end, $expectedCostOfGoods)
    {
        $product = $this->factory()->catalog()->getProduct('1');
        $store = $this->factory()->store()->getStore('701');
        $storeProductId = $this->getStoreProductRepository()->getIdByStoreAndProduct($store, $product);

        $this->factory()
            ->invoice()
                ->createInvoice(array('date' => '2014-01-12 12:23:12'), $store->id)
                ->createInvoiceProduct($product->id, 5, 11.09)
            ->persist()
                ->createInvoice(array('date' => '2014-01-12 13:23:12'), $store->id)
                ->createInvoiceProduct($product->id, 3, 12.13)
            ->persist()
                ->createInvoice(array('date' => '2014-01-12 14:23:12'), $store->id)
                ->createInvoiceProduct($product->id, 2, 10.09)
            ->flush();

        $this->processedAllJobs();

        $startIndex = $this->getNumericFactory()->createQuantity($start);
        $endIndex = $this->getNumericFactory()->createQuantity($end);
        $costOfGoods = $this->getCostOfGoodsCalculator()->calculateByIndexRange(
            $storeProductId,
            $startIndex,
            $endIndex
        );
        $this->assertSame($expectedCostOfGoods, $costOfGoods->toNumber());
    }

    /**
     * @return array
     */
    public function costOfGoodsCalculateByIndexRangeProvider()
    {
        return array(
            '0,5 - exact range #1' => array(
                0,
                5,
                55.45
            ),
            '5,8 - exact range #2' => array(
                5,
                8,
                36.39
            ),
            '8,10 - exact range #3' => array(
                8,
                10,
                20.18,
            ),
            '0,1 - start/inside #1' => array(
                0,
                1,
                11.09
            ),
            '1,3 - inside #1' => array(
                1,
                3,
                22.18
            ),
            '0,6' => array(
                0,
                6,
                67.58
            ),
            '4,6' => array(
                4,
                6,
                23.22
            ),
            '5,6' => array(
                5,
                6,
                12.13
            ),
            '6,8' => array(
                6,
                8,
                24.26
            ),
            '10,11' => array(
                10,
                11,
                10.09
            ),
            '1,9' => array(
                1,
                9,
                90.84
            ),
            '0,11' => array(
                1,
                9,
                90.84
            ),
            '11,14' => array(
                11,
                14,
                30.27
            )
        );
    }

    public function testCostOfGoodsCalculate()
    {
        $store = $this->factory()->store()->getStore('1');
        $product = $this->factory()->catalog()->getProduct('1');
        $this->factory()->catalog()->getProduct('2');

        $this->factory()
            ->invoice()
                ->createInvoice(array('date' => date('c', strtotime('now'))), $store->id)
                ->createInvoiceProduct($product->id, 1.345, 23.77)
            ->flush();
        $this->factory()
            ->stockIn()
                ->createStockIn($store, date('c', strtotime('now')))
                ->createStockInProduct($product->id, 2.332, 0.1)
            ->flush();
        $this->factory()
            ->invoice()
                ->createInvoice(array('date' => date('c', strtotime('now'))), $store->id)
                ->createInvoiceProduct($product->id, 3, 13.3)
                ->createInvoiceProduct($product->id, 4.23, 14)
            ->flush();
        $this->factory()
            ->invoice()
                ->createInvoice(array('date' => date('c', strtotime('now'))), $store->id)
                ->createInvoiceProduct($product->id, 5.7, 17.99)
            ->flush();
        // Total quantity = 16.607

        $sale1 = $this->factory()
            ->receipt()
                ->createSale($store, '-1 hour')
                ->createReceiptProduct($product->id, 2.435, 2.33)
                ->createReceiptProduct($product->id, 1.32, 2.33)
                ->createReceiptProduct($product->id, 0.76, 2.33)
            ->flush();

        $sale2 = $this->factory()
            ->receipt()
                ->createSale($store, 'now')
                ->createReceiptProduct($product->id, 1, 2.33)
                ->createReceiptProduct($product->id, 1.7, 2.33)
            ->flush();

        // Total quantity = 7.215

        $this->factory()->flush();

        // Calculate CostOfGoods

        $this->processedAllJobs();

        $this->assertCostOfGood($sale1->products[0], '32.08');
        $this->assertCostOfGood($sale1->products[1], '1.16');
        $this->assertCostOfGood($sale1->products[2], '10.11');
        $this->assertCostOfGood($sale2->products[0], '13.30');
        $this->assertCostOfGood($sale2->products[1], '22.98');
    }

    public function testCostOfGoodsCalculateAfterInsertOldReceipts()
    {
        $store = $this->factory()->store()->getStore('1');
        $product = $this->factory()->catalog()->getProduct('1');

        $this->factory()
            ->invoice()
                ->createInvoice(array(), $store->id)
                ->createInvoiceProduct($product->id, 5, 100)
                ->createInvoiceProduct($product->id, 5, 150)
                ->createInvoiceProduct($product->id, 5, 200)
            ->flush();

        $sale = $this->factory()
            ->receipt()
                ->createSale($store, '2014-01-10 12:23:12')
                ->createReceiptProduct($product->id, 6, 250)
            ->flush();

        $this->processedAllJobs();

        $this->assertCostOfGood($sale->products[0], '650.00');

        $saleBehindhand1 = $this->factory()
            ->receipt()
                ->createSale($store, '2014-01-09 12:23:12')
                ->createReceiptProduct($product->id, 7, 250)
            ->flush();

        $this->processedAllJobs();

        $this->assertCostOfGood($saleBehindhand1->products[0], '800.00');
        $this->assertCostOfGood($sale->products[0], '1050.00');

        $saleBehindhand2 = $this->factory()
            ->receipt()
                ->createSale($store, '2014-01-09 16:23:12')
                ->createReceiptProduct($product->id, 2, 250)
            ->flush();

        $this->processedAllJobs();

        $this->assertCostOfGood($saleBehindhand1->products[0], '800.00');
        $this->assertCostOfGood($saleBehindhand2->products[0], '300.00');
        $this->assertCostOfGood($sale->products[0], '1150.00');
    }

    public function testCostOfGoodsCalculateDuplicateReceipt()
    {
        $store = $this->factory()->store()->getStore('1');
        $product = $this->factory()->catalog()->getProduct('1');

        $this->factory()
            ->invoice()
                ->createInvoice(array(), $store->id)
                ->createInvoiceProduct($product->id, 5, 100)
                ->createInvoiceProduct($product->id, 5, 150)
                ->createInvoiceProduct($product->id, 5, 200)
            ->flush();


        $sale1 = $this->factory()
            ->receipt()
                ->createSale($store, '2014-01-09 12:23:12')
                ->createReceiptProduct($product->id, 7, 250)
            ->flush();

        $sale2 = $this->factory()
            ->receipt()
                ->createSale($store, '2014-01-09 16:23:12')
                ->createReceiptProduct($product->id, 2, 250)
            ->flush();

        $sale3 = $this->factory()
            ->receipt()
                ->createSale($store, '2014-01-10 12:23:12')
                ->createReceiptProduct($product->id, 6, 250)
            ->flush();

        $this->processedAllJobs();

        $this->assertCostOfGood($sale1->products[0], '800.00');
        $this->assertCostOfGood($sale2->products[0], '300.00');
        $this->assertCostOfGood($sale3->products[0], '1150.00');

        $this->factory()->receipt()->deleteReceipt($sale1);

        $this->processedAllJobs();

        $this->assertCostOfGood($sale2->products[0], '200.00');
        $this->assertCostOfGood($sale3->products[0], '750.00');

        $sale4 = $this->factory()
            ->receipt()
                ->createSale($store, '2014-01-09 12:23:12')
                ->createReceiptProduct($product->id, 4, 250)
            ->flush();

        $this->processedAllJobs();

        $this->assertCostOfGood($sale4->products[0], '400.00');
        $this->assertCostOfGood($sale2->products[0], '250.00');
        $this->assertCostOfGood($sale3->products[0], '1000.00');

        $this->factory()->receipt()->deleteReceipt($sale2);
        $this->processedAllJobs();

        $this->assertCostOfGood($sale4->products[0], '400.00');
        $this->assertCostOfGood($sale3->products[0], '850.00');
    }

    public function testCostOfGoodsCalculateEditInvoice()
    {
        $store = $this->factory()->store()->getStore('1');
        $product = $this->factory()->catalog()->getProduct('1');
        $productOther = $this->factory()->catalog()->getProduct('Other');

        $invoice1 = $this->factory()
            ->invoice()
                ->createInvoice(array('date' => '2014-01-01 12:56'), $store->id)
                ->createInvoiceProduct($product->id, 5, 100)
                ->createInvoiceProduct($productOther->id, 1, 1)
            ->flush();

        $invoice2 = $this->factory()
            ->invoice()
                ->createInvoice(array('date' => '2014-01-02 12:56'), $store->id)
                ->createInvoiceProduct($product->id, 5, 150)
                ->createInvoiceProduct($productOther->id, 1, 1)
            ->flush();

        $this->factory()
            ->invoice()
                ->createInvoice(array('date' => '2014-01-03 12:56'), $store->id)
                ->createInvoiceProduct($product->id, 10, 200)
            ->flush();

        $sale1 = $this->factory()
            ->receipt()
                ->createSale($store, '2014-01-09 12:23:12')
                ->createReceiptProduct($product->id, 7, 250)
            ->flush();

        $sale2 = $this->factory()
            ->receipt()
                ->createSale($store, '2014-01-09 16:23:12')
                ->createReceiptProduct($product->id, 2, 250)
            ->flush();

        $sale3 = $this->factory()
            ->receipt()
                ->createSale($store, '2014-01-10 12:23:12')
                ->createReceiptProduct($product->id, 6, 250)
            ->flush();

        $this->processedAllJobs();

        $this->assertCostOfGood($sale1->products[0], '800.00');
        $this->assertCostOfGood($sale2->products[0], '300.00');
        $this->assertCostOfGood($sale3->products[0], '1150.00');

        // Edit invoice product price and quantity
        $this->factory()
            ->invoice()
                ->editInvoice($invoice1->id)
                ->editInvoiceProduct(0, $product->id, 6, 50)
            ->flush();

        $this->processedAllJobs();

        $this->assertCostOfGood($sale1->products[0], '450.00');
        $this->assertCostOfGood($sale2->products[0], '300.00');
        $this->assertCostOfGood($sale3->products[0], '1100.00');


        // Edit invoice product change product
        $this->factory()
            ->invoice()
                ->editInvoice($invoice1->id)
                ->editInvoiceProduct(0, $productOther->id, 1, 11)
            ->flush();

        $this->processedAllJobs();

        $this->assertCostOfGood($sale1->products[0], '1150.00');
        $this->assertCostOfGood($sale2->products[0], '400.00');
        $this->assertCostOfGood($sale3->products[0], '1200.00');


        // Edit invoice product delete not first
        $this->factory()
            ->clear()
            ->invoice()
                ->editInvoice($invoice1->id)
                ->editInvoiceProduct(0, $product->id, 5, 100)
            ->flush();

        $this->processedAllJobs();

        $this->assertCostOfGood($sale1->products[0], '800.00');
        $this->assertCostOfGood($sale2->products[0], '300.00');
        $this->assertCostOfGood($sale3->products[0], '1150.00');

        $this->factory()
            ->clear()
            ->invoice()
                ->editInvoice($invoice2->id)
                ->deleteInvoiceProduct(0)
            ->flush();

        $this->processedAllJobs();

        $this->assertCostOfGood($sale1->products[0], '900.00');
        $this->assertCostOfGood($sale2->products[0], '400.00');
        $this->assertCostOfGood($sale3->products[0], '1200.00');


        // Edit invoice product delete first
        $this->factory()
            ->clear()
            ->invoice()
                ->editInvoice($invoice2->id)
                ->createInvoiceProduct($product->id, 5, 150)
            ->flush();

        $this->processedAllJobs();

        $this->assertCostOfGood($sale1->products[0], '800.00');
        $this->assertCostOfGood($sale2->products[0], '300.00');
        $this->assertCostOfGood($sale3->products[0], '1150.00');

        $this->factory()
            ->clear()
            ->invoice()
                ->editInvoice($invoice1->id)
                ->deleteInvoiceProduct(0)
            ->flush();

        $this->processedAllJobs();

        $this->assertCostOfGood($sale1->products[0], '1150.00');
        $this->assertCostOfGood($sale2->products[0], '400.00');
        $this->assertCostOfGood($sale3->products[0], '1200.00');
    }

    public function testCostOfGoodsCalculateEditInvoiceDate()
    {
        $store = $this->factory()->store()->getStore('1');
        $product = $this->factory()->catalog()->getProduct('1');
        $this->factory()->catalog()->getProduct('Other');

        $storeProductId = $this->getStoreProductRepository()->getIdByStoreIdAndProductId($store->id, $product->id);

        $invoice1 = $this->factory()
            ->invoice()
                ->createInvoice(array('date' => '2014-01-01 12:56'), $store->id)
                ->createInvoiceProduct($product->id, 5, 100, $store->id)
            ->flush();
        $invoice2 = $this->factory()
            ->invoice()
                ->createInvoice(array('date' => '2014-01-02 12:56'), $store->id)
                ->createInvoiceProduct($product->id, 5, 150, $store->id)
            ->flush();

        $invoice3 = $this->factory()
            ->invoice()
                ->createInvoice(array('date' => '2014-01-03 12:56'), $store->id)
                ->createInvoiceProduct($product->id, 10, 200, $store->id)
            ->flush();

        $sale1 = $this->factory()
            ->receipt()
                ->createSale($store, '2014-01-09 12:23:12')
                ->createReceiptProduct($product->id, 7, 250)
            ->flush();

        $sale2 = $this->factory()
            ->receipt()
                ->createSale($store, '2014-01-09 16:23:12')
                ->createReceiptProduct($product->id, 2, 250)
            ->flush();

        $sale3 = $this->factory()
            ->receipt()
                ->createSale($store, '2014-01-10 12:23:12')
                ->createReceiptProduct($product->id, 6, 250)
            ->flush();

        $this->processedAllJobs();

        $this->assertCostOfGood($sale1->products[0], '800.00');
        $this->assertCostOfGood($sale2->products[0], '300.00');
        $this->assertCostOfGood($sale3->products[0], '1150.00');

        $this->factory()
            ->clear()
            ->invoice()
                ->editInvoice($invoice2->id, array('date' => '2014-01-01 10:00'))
            ->flush();

        $this->processedAllJobs();

        $this->assertCostOfGood($sale1->products[0], '950.00');
        $this->assertCostOfGood($sale2->products[0], '200.00');
        $this->assertCostOfGood($sale3->products[0], '1100.00');

        $this->factory()
            ->clear()
            ->invoice()
                ->editInvoice($invoice2->id, array('date' => '2014-01-02 12:56'))
            ->flush();

        $this->assertStoreProductTrialBalance(
            $storeProductId,
            InvoiceProduct::TYPE,
            array(
                array(
                    'reasonId' => $invoice1->products[0]->id,
                    'status' => TrialBalance::PROCESSING_STATUS_UNPROCESSED
                ),
                array(
                    'reasonId' => $invoice2->products[0]->id,
                    'status' => TrialBalance::PROCESSING_STATUS_UNPROCESSED
                ),
                array(
                    'reasonId' => $invoice3->products[0]->id,
                    'status' => TrialBalance::PROCESSING_STATUS_OK
                ),
            )
        );

        $this->processedAllJobs();

        $this->assertCostOfGood($sale1->products[0], '800.00');
        $this->assertCostOfGood($sale2->products[0], '300.00');
        $this->assertCostOfGood($sale3->products[0], '1150.00');
    }

    public function testCostOfGoodsCalculateOutOfStock()
    {
        $store = $this->factory()->store()->getStore('1');
        $product = $this->factory()->catalog()->createProduct(array('purchasePrice' => 100));
        $this->factory()->catalog()->getProduct('Other');

        $sale1 = $this->factory()
            ->receipt()
                ->createSale($store, '2014-01-09 12:23:12')
                ->createReceiptProduct($product->id, 7, 250)
            ->flush();

        $this->processedAllJobs();

        $trialBalanceSaleProduct1 = $this->getTrialBalanceRepository()
            ->findOneByReasonTypeReasonId($sale1->products[0]->id, SaleProduct::TYPE);
        $this->assertEquals(700, $trialBalanceSaleProduct1->costOfGoods->toNumber());

        $this->factory()
            ->invoice()
                ->createInvoice(array('date' => '2014-01-01 12:56'), $store->id)
                ->createInvoiceProduct($product->id, 5, 150)
            ->flush();

        $this->processedAllJobs();

        $this->assertCostOfGood($sale1->products[0], '1050.00');

        $this->factory()
            ->invoice()
                ->createInvoice(array('date' => '2014-01-02 12:56'), $store->id)
                ->createInvoiceProduct($product->id, 1, 200)
            ->flush();

        $this->processedAllJobs();

        $this->assertCostOfGood($sale1->products[0], '1150.00');
    }

    public function testCostOfGoodsCalculateProductWithoutPurchasePrice()
    {
        $store = $this->factory()->store()->getStore('1');
        $product = $this->factory()->catalog()->createProduct(array('purchasePrice' => ''));
        $this->factory()->catalog()->getProduct('Other');

        $sale1 = $this->factory()
            ->receipt()
            ->createSale($store, '2014-01-09 12:23:12')
            ->createReceiptProduct($product->id, 7, 250)
            ->flush();

        $this->processedAllJobs();

        $trialBalanceSaleProduct1 = $this->getTrialBalanceRepository()
            ->findOneByReasonTypeReasonId($sale1->products[0]->id, SaleProduct::TYPE);
        $this->assertEquals(0, $trialBalanceSaleProduct1->costOfGoods->toNumber());

        $this->factory()
            ->invoice()
            ->createInvoice(array('date' => '2014-01-01 12:56'), $store->id)
            ->createInvoiceProduct($product->id, 5, 150)
            ->flush();

        $this->processedAllJobs();

        $this->assertCostOfGood($sale1->products[0], '1050.00');

        $this->factory()
            ->invoice()
            ->createInvoice(array('date' => '2014-01-02 12:56'), $store->id)
            ->createInvoiceProduct($product->id, 1, 200)
            ->flush();

        $this->processedAllJobs();

        $this->assertCostOfGood($sale1->products[0], '1150.00');
    }

    public function testCostOfGoodsCalculateWithAllStockMovementProductTypes()
    {
        $store = $this->factory()->store()->getStore();
        $product = $this->factory()->catalog()->getProduct();

        $this->factory()
            ->invoice()
                ->createInvoice(array('date' => date('c', strtotime('-200 hour'))), $store->id)
                ->createInvoiceProduct($product->id, 3, 11)
            ->flush();

        $this->factory()
            ->stockIn()
                ->createStockIn($store, date('c', strtotime('-190 hour')))
                ->createStockInProduct($product->id, 5, 15)
            ->flush();

        $sale = $this->factory()
            ->receipt()
                ->createSale($store, date('c', strtotime('-180 hour')))
                ->createReceiptProduct($product->id, 4, 20)
            ->flush();

        $writeOff = $this->factory()
            ->writeOff()
                ->createWriteOff($store, date('c', strtotime('-170 hour')))
                ->createWriteOffProduct($product->id, 2, 18)
            ->flush();

        $supplierReturn = $this->factory()
            ->supplierReturn()
                ->createSupplierReturn($store, date('c', strtotime('-160 hour')))
                ->createSupplierReturnProduct($product->id, 2, 15)
            ->flush();

        $this->processedAllJobs();

        $this->assertCostOfGood($sale->products[0], '48.00');
        $this->assertCostOfGood($writeOff->products[0], '30.00');
        $this->assertCostOfGood($supplierReturn->products[0], '30.00');


        $this->factory()
            ->receipt()
                ->createReturn($store, date('c', strtotime('-150 hour')), $sale)
                ->createReceiptProduct($product->id, 1)
            ->flush();

        $sale2 = $this->factory()
            ->receipt()
                ->createSale($store, date('c', strtotime('-140 hour')))
                ->createReceiptProduct($product->id, 1, 20)
            ->flush();

        $this->processedAllJobs();

        $this->assertCostOfGood($sale->products[0], '48.00');
        $this->assertCostOfGood($writeOff->products[0], '30.00');
        $this->assertCostOfGood($supplierReturn->products[0], '30.00');
        $this->assertCostOfGood($sale2->products[0], '12.00');
    }

    /**
     * @param string $storeProductId
     * @param string $reasonType
     * @param array $expectations
     */
    protected function assertStoreProductTrialBalance($storeProductId, $reasonType, array $expectations)
    {
        $trialBalances = $this->getTrialBalanceRepository()->findByStoreProductIdAndReasonType(
            $storeProductId,
            $reasonType
        );
        $this->assertCount(count($expectations), $trialBalances);

        foreach ($expectations as $expected) {
            /* @var TrialBalance $trialBalance */
            $trialBalance = $trialBalances->getNext();
            $this->assertEquals($expected['reasonId'], $trialBalance->reason->id);
            $this->assertEquals($expected['status'], $trialBalance->processingStatus, 'Status does not match');
        }
    }

    /**
     * @param StockMovementProduct $stockMovementProduct
     * @param string $expectedCostOfGood
     */
    protected function assertCostOfGood(StockMovementProduct $stockMovementProduct, $expectedCostOfGood)
    {
        $trialBalance = $this->getTrialBalanceRepository()->findOneByStockMovementProduct($stockMovementProduct);
        $this->assertSame($expectedCostOfGood, $trialBalance->costOfGoods->toString());
    }

    /**
     * @return TrialBalanceRepository
     */
    protected function getTrialBalanceRepository()
    {
        return $this->getContainer()->get('lighthouse.core.document.repository.trial_balance');
    }

    /**
     * @return CostOfGoodsCalculator
     */
    protected function getCostOfGoodsCalculator()
    {
        return $this->getContainer()->get('lighthouse.core.document.trial_balance.calculator');
    }

    /**
     * @return StoreProductRepository
     */
    protected function getStoreProductRepository()
    {
        return $this->getContainer()->get('lighthouse.core.document.repository.store_product');
    }

    /**
     * @return NumericFactory
     */
    protected function getNumericFactory()
    {
        return $this->getContainer()->get('lighthouse.core.types.numeric.factory');
    }
}
