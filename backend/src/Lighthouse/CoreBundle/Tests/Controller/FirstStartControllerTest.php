<?php

namespace Lighthouse\CoreBundle\Tests\Controller;

use Lighthouse\CoreBundle\Test\Assert;
use Lighthouse\CoreBundle\Test\Console\ApplicationTester;
use Lighthouse\CoreBundle\Test\WebTestCase;

class FirstStartControllerTest extends WebTestCase
{
    public function testGetFirstStartClean()
    {
        $accessToken = $this->factory()->oauth()->authAsProjectUser();

        $response1 = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/firstStart'
        );

        $this->assertResponseCode(200);

        Assert::assertNotJsonHasPath('id', $response1);
        Assert::assertJsonPathEquals(false, 'complete', $response1);
        Assert::assertJsonPathCount(0, 'stores.*', $response1);

        $response2 = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/firstStart'
        );

        $this->assertResponseCode(200);

        $this->assertSame($response1, $response2);
    }

    public function testGetFirstStartStore()
    {
        $accessToken = $this->factory()->oauth()->authAsProjectUser();

        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/firstStart'
        );

        $this->assertResponseCode(200);

        Assert::assertNotJsonHasPath('id', $response);
        Assert::assertJsonPathEquals(false, 'complete', $response);
        Assert::assertJsonPathCount(0, 'stores.*', $response);

        $store1 = $this->factory()->store()->getStore('1');

        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/firstStart'
        );

        $this->assertResponseCode(200);

        Assert::assertNotJsonHasPath('id', $response);
        Assert::assertJsonPathEquals(false, 'complete', $response);
        Assert::assertJsonPathEquals($store1->id, 'stores.0.store.id', $response, 1);
        Assert::assertJsonPathCount(1, 'stores.*', $response);

        $store2 = $this->factory()->store()->getStore('2');

        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/firstStart'
        );

        $this->assertResponseCode(200);

        Assert::assertNotJsonHasPath('id', $response);
        Assert::assertJsonPathEquals(false, 'complete', $response);
        Assert::assertJsonPathEquals($store1->id, 'stores.0.store.id', $response, 1);
        Assert::assertJsonPathEquals($store2->id, 'stores.1.store.id', $response, 1);
        Assert::assertJsonPathCount(2, 'stores.*', $response);
    }

    public function testGetFirstStartInvoice()
    {
        $stores = $this->factory()->store()->getStores(array('1', '2'));

        $accessToken = $this->factory()->oauth()->authAsProjectUser();

        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/firstStart'
        );

        $this->assertResponseCode(200);

        Assert::assertJsonPathCount(2, 'stores.*.store', $response);
        Assert::assertNotJsonHasPath('stores.*.sale', $response);
        Assert::assertNotJsonHasPath('stores.*.costOfInventory', $response);

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

        $this->runRecalculateCommand();

        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/firstStart'
        );

        $this->assertResponseCode(200);

        Assert::assertJsonPathCount(2, 'stores.*.store', $response);

        Assert::assertJsonPathEquals('2200.00', 'stores.0.costOfInventory', $response);
        Assert::assertJsonPathEquals('3175.00', 'stores.1.costOfInventory', $response);
    }

    public function testGetFirstStartSale()
    {
        $stores = $this->factory()->store()->getStores(array('1', '2'));

        $accessToken = $this->factory()->oauth()->authAsProjectUser();

        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/firstStart'
        );

        $this->assertResponseCode(200);

        Assert::assertJsonPathCount(2, 'stores.*.store', $response);
        Assert::assertNotJsonHasPath('stores.*.sale', $response);
        Assert::assertNotJsonHasPath('stores.*.costOfInventory', $response);

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

        $sale1 = $this->factory()
            ->receipt()
                ->createSale($stores['1'])
                ->createReceiptProduct($products['1']->id, 2, 69.99)
                ->createReceiptProduct($products['2']->id, 5.678, 44.49)
            ->flush();

        $sale2 = $this->factory()
            ->receipt()
                ->createSale($stores['2'])
                ->createReceiptProduct($products['2']->id, 5, 49.99)
                ->createReceiptProduct($products['3']->id, 2.333, 39.89)
            ->flush();

        $this->runRecalculateCommand();

        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/firstStart'
        );

        $this->assertResponseCode(200);

        Assert::assertJsonPathCount(2, 'stores.*.store', $response);

        Assert::assertJsonPathEquals($sale1->id, 'stores.0.sale.receipt.id', $response);
        Assert::assertJsonPathEquals('392.59', 'stores.0.sale.grossSales', $response);
        Assert::assertJsonPathEquals('327.12', 'stores.0.sale.costOfGoods', $response);
        Assert::assertJsonPathEquals('65.47', 'stores.0.sale.grossMargin', $response);

        Assert::assertJsonPathEquals($sale2->id, 'stores.1.sale.receipt.id', $response);
        Assert::assertJsonPathEquals('343.01', 'stores.1.sale.grossSales', $response);
        Assert::assertJsonPathEquals('306.66', 'stores.1.sale.costOfGoods', $response);
        Assert::assertJsonPathEquals('36.35', 'stores.1.sale.grossMargin', $response);

        Assert::assertJsonPathEquals('1872.88', 'stores.0.costOfInventory', $response);
        Assert::assertJsonPathEquals('2868.34', 'stores.1.costOfInventory', $response);
    }

    public function testPutFirstStartComplete()
    {
        $accessToken = $this->factory()->oauth()->authAsProjectUser();

        $getResponse = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/firstStart'
        );

        $this->assertResponseCode(200);

        Assert::assertJsonPathCount(0, 'stores.*.store', $getResponse);
        Assert::assertJsonPathEquals(false, 'complete', $getResponse);

        $this->factory()->store()->getStores(array('1', '2'));

        $getResponse = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/firstStart'
        );

        Assert::assertJsonPathCount(2, 'stores.*.store', $getResponse);
        Assert::assertJsonPathEquals(false, 'complete', $getResponse);
        Assert::assertNotJsonHasPath('stores.*.sale', $getResponse);
        Assert::assertNotJsonHasPath('stores.*.costOfInventory', $getResponse);

        $putResponse = $this->clientJsonRequest(
            $accessToken,
            'PUT',
            '/api/1/firstStart',
            array(
                'complete' => true,
            )
        );

        $this->assertResponseCode(200);

        Assert::assertJsonPathCount(2, 'stores.*.store', $getResponse);
        Assert::assertJsonPathEquals(true, 'complete', $putResponse);
        Assert::assertNotJsonHasPath('stores.*.sale', $putResponse);
        Assert::assertNotJsonHasPath('stores.*.costOfInventory', $putResponse);

        $getResponse = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/firstStart'
        );

        $this->assertResponseCode(200);

        $this->assertSame($putResponse, $getResponse);
    }

    public function testPutFirstStartCompleteUndo()
    {
        $accessToken = $this->factory()->oauth()->authAsProjectUser();

        $this->factory()->store()->getStores(array('1', '2'));

        $putResponse = $this->clientJsonRequest(
            $accessToken,
            'PUT',
            '/api/1/firstStart',
            array(
                'complete' => true,
            )
        );

        $this->assertResponseCode(200);

        Assert::assertJsonPathCount(2, 'stores.*.store', $putResponse);
        Assert::assertJsonPathEquals(true, 'complete', $putResponse);

        $undoResponse = $this->clientJsonRequest(
            $accessToken,
            'PUT',
            '/api/1/firstStart',
            array(
                'complete' => false,
            )
        );

        $this->assertResponseCode(200);

        Assert::assertJsonPathCount(2, 'stores.*.store', $undoResponse);
        Assert::assertJsonPathEquals(false, 'complete', $undoResponse);

        $getResponse = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/firstStart'
        );

        $this->assertResponseCode(200);

        $this->assertSame($undoResponse, $getResponse);
    }

    /**
     * @return ApplicationTester
     */
    protected function runRecalculateCommand()
    {
        return $this->createConsoleTester(false, true)->runCommand('lighthouse:reports:recalculate');
    }
}
