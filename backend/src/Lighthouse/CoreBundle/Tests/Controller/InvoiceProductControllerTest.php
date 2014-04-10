<?php

namespace Lighthouse\CoreBundle\Tests\Controller;

use Lighthouse\CoreBundle\Document\Product\Store\StoreProductMetricsCalculator;
use Lighthouse\CoreBundle\Test\Assert;
use Lighthouse\CoreBundle\Test\WebTestCase;
use Lighthouse\CoreBundle\Versionable\VersionRepository;

class InvoiceProductControllerTest extends WebTestCase
{
    /**
     * @param string $supplierId
     * @param string $productId
     * @param string|float $quantity
     * @param string|float $price
     * @return array
     */
    protected function getInvoiceData($supplierId, $productId, $quantity = '10', $price = '5.99')
    {
        return array(
            'supplier' => $supplierId,
            'acceptanceDate' => '2013-03-18 12:56',
            'accepter' => 'Приемных Н.П.',
            'legalEntity' => 'ООО "Магазин"',
            'supplierInvoiceNumber' => '1248373',
            'includesVAT' => true,
            'products' => array(
                array(
                    'quantity' => $quantity,
                    'priceEntered' => $price,
                    'product' => $productId,
                )
            )
        );
    }

    /**
     * @dataProvider productTotalPriceCalculationProvider
     */
    public function testProductTotalPriceCalculation($quantity, $price, $totalPrice)
    {
        $store = $this->factory()->store()->getStore();
        $supplier = $this->factory()->supplier()->getSupplier();
        $productId = $this->createProduct();

        $invoiceData = $this->getInvoiceData($supplier->id, $productId, $quantity, $price);

        $accessToken = $this->factory()->oauth()->authAsDepartmentManager($store->id);

        $responseJson = $this->clientJsonRequest(
            $accessToken,
            'POST',
            '/api/1/stores/' . $store->id . '/invoices',
            $invoiceData
        );

        $this->assertResponseCode(201);
        Assert::assertJsonHasPath('id', $responseJson);
        Assert::assertJsonPathEquals($quantity, 'products.0.quantity', $responseJson);
        Assert::assertJsonPathEquals($price, 'products.0.priceEntered', $responseJson);
        Assert::assertJsonPathEquals($totalPrice, 'products.0.totalPrice', $responseJson);
        Assert::assertJsonPathEquals($productId, 'products.0.product.id', $responseJson);
    }

    /**
     * @return array
     */
    public function productTotalPriceCalculationProvider()
    {
        return array(
            '793.32'   => array('11',       '72.12', '793.32'),
            '72.12'    => array('1',        '72.12', '72.12'),
            '216.36'   => array('3',        '72.12', '216.36'),
            '649.08'   => array('9',        '72.12', '649.08'),
            '72192.12' => array('1001',     '72.12', '72192.12'),
            '72758.99' => array('1009',     '72.11', '72758.99'),
            '72.00'    => array('1',        '72.00', '72.00'),
            '913.70'   => array('12.671',   '72.11', '913.70'),
        );
    }

    public function testPostActionNotExistingField()
    {
        $store = $this->factory()->store()->getStore();
        $supplier = $this->factory()->supplier()->getSupplier();
        $productId = $this->createProduct();

        $invoiceData = $this->getInvoiceData($supplier->id, $productId);
        $invoiceData['products'][0]['dummy'] = 'dummy';
        $invoiceData['products'][0]['foo'] = 'foo';

        $accessToken = $this->factory()->oauth()->authAsDepartmentManager($store->id);

        $response = $this->clientJsonRequest(
            $accessToken,
            'POST',
            '/api/1/stores/' . $store->id . '/invoices',
            $invoiceData
        );

        $this->assertResponseCode(400);
        Assert::assertJsonPathEquals(
            'Эта форма не должна содержать дополнительных полей: "dummy", "foo"',
            'errors.0',
            $response
        );
    }

    public function testPostActionsNotExistingProduct()
    {
        $supplier = $this->factory()->supplier()->getSupplier();
        $store = $this->factory()->store()->getStore();

        $invoiceData = $this->getInvoiceData($supplier->id, 'invalid');

        $accessToken = $this->factory->oauth()->authAsDepartmentManager($store->id);

        $response = $this->clientJsonRequest(
            $accessToken,
            'POST',
            '/api/1/stores/' . $store->id . '/invoices',
            $invoiceData
        );

        $this->assertResponseCode(400);
        Assert::assertJsonPathEquals(
            'Такого товара не существует',
            'children.products.children.0.children.product.errors.0',
            $response
        );
    }

    public function testInvoiceTotalsAreUpdated()
    {
        $store = $this->factory()->store()->getStore();
        $supplier = $this->factory()->supplier()->getSupplier();

        $productIds = $this->createProductsBySku(array('1', '2', '3'));

        $accessToken = $this->factory->oauth()->authAsDepartmentManager($store->id);

        // add first product
        $invoiceData = $this->getInvoiceData($supplier->id, $productIds[1], 10, '11.12');

        $response = $this->clientJsonRequest(
            $accessToken,
            'POST',
            '/api/1/stores/' . $store->id . '/invoices',
            $invoiceData
        );

        $this->assertResponseCode(201);

        $invoiceId = $response['id'];
        Assert::assertJsonPathEquals(1, 'itemsCount', $response);
        Assert::assertJsonPathEquals(111.2, 'sumTotal', $response);

        // Add second product
        $invoiceData['products'][1] = array(
            'product' => $productIds['2'],
            'quantity' => 5,
            'priceEntered' => '12.76'
        );

        $response = $this->clientJsonRequest(
            $accessToken,
            'PUT',
            '/api/1/stores/' . $store->id . '/invoices/' . $invoiceId,
            $invoiceData
        );

        $this->assertResponseCode(200);

        Assert::assertJsonPathEquals('2', 'itemsCount', $response);
        Assert::assertJsonPathEquals('175', 'sumTotal', $response);

        // Add third product
        $invoiceData['products'][2] = array(
            'product' => $productIds['3'],
            'quantity' => 1,
            'priceEntered' => '5.99'
        );

        $response = $this->clientJsonRequest(
            $accessToken,
            'PUT',
            '/api/1/stores/' . $store->id . '/invoices/' . $invoiceId,
            $invoiceData
        );

        $this->assertResponseCode(200);

        Assert::assertJsonPathEquals('3', 'itemsCount', $response);
        Assert::assertJsonPathEquals('180.99', 'sumTotal', $response);
    }

    public function testProductAmountIsUpdatedOnInvoiceProductPost()
    {
        $store = $this->factory()->store()->getStore();
        $supplier = $this->factory()->supplier()->getSupplier();

        $productId = $this->createProduct();

        $accessToken = $this->factory->oauth()->authAsDepartmentManager($store->id);

        // add first product
        $invoiceData = $this->getInvoiceData($supplier->id, $productId, 10, '11.12');

        $response = $this->clientJsonRequest(
            $accessToken,
            'POST',
            '/api/1/stores/' . $store->id . '/invoices',
            $invoiceData
        );

        $this->assertResponseCode(201);

        $invoiceId = $response['id'];
        Assert::assertJsonPathEquals(1, 'itemsCount', $response);
        Assert::assertJsonPathEquals(111.2, 'sumTotal', $response);
        $this->assertStoreProductTotals($store->id, $productId, 10, '11.12');

        // Add second product
        $invoiceData['products'][1] = array(
            'product' => $productId,
            'quantity' => 5,
            'priceEntered' => '12.76'
        );

        $response = $this->clientJsonRequest(
            $accessToken,
            'PUT',
            '/api/1/stores/' . $store->id . '/invoices/' . $invoiceId,
            $invoiceData
        );

        $this->assertResponseCode(200);

        Assert::assertJsonPathEquals('2', 'itemsCount', $response);
        Assert::assertJsonPathEquals('175', 'sumTotal', $response);
        $this->assertStoreProductTotals($store->id, $productId, 15, '12.76');

        // Add third product
        $invoiceData['products'][2] = array(
            'product' => $productId,
            'quantity' => 1,
            'priceEntered' => '5.99'
        );

        $response = $this->clientJsonRequest(
            $accessToken,
            'PUT',
            '/api/1/stores/' . $store->id . '/invoices/' . $invoiceId,
            $invoiceData
        );

        $this->assertResponseCode(200);

        Assert::assertJsonPathEquals('3', 'itemsCount', $response);
        Assert::assertJsonPathEquals('180.99', 'sumTotal', $response);
        $this->assertStoreProductTotals($store->id, $productId, 16, '5.99');
    }

    /**
     * @dataProvider validationProvider
     */
    public function testPostActionValidation($expectedCode, array $data, array $assertions = array())
    {
        $invoiceId = $this->createInvoice(array(), $this->storeId, $this->departmentManager);
        $productId = $this->createProduct();
        $invoiceData = $this->getInvoiceData($supplier->id, $productId, 10, 17.68);

        $invoiceData['products'][0] = $data + $invoiceData['products'][0];

        $accessToken = $this->factory()->oauth()->authAsDepartmentManager($store->id);

        $response = $this->clientJsonRequest(
            $accessToken,
            'POST',
            '/api/1/stores/' . $store->id . '/invoices',
            $invoiceData
        );

        $this->assertResponseCode($expectedCode);
        $this->performJsonAssertions($response, $assertions, true);
    }

    /**
     * @return array
     */
    public function validationProvider()
    {
        return array(
            /***********************************************************************************************
             * 'quantity'
             ***********************************************************************************************/
            'valid quantity 7' => array(
                201,
                array('quantity' => 7),
            ),
            'empty quantity' => array(
                400,
                array('quantity' => ''),
                array(
                    'children.products.children.0.children.quantity.errors.0'
                    =>
                    'Заполните это поле'
                )
            ),
            'negative quantity -10' => array(
                400,
                array('quantity' => -10),
                array(
                    'children.products.children.0.children.quantity.errors.0'
                    =>
                    'Значение должно быть больше 0'
                )
            ),
            'negative quantity -1' => array(
                400,
                array('quantity' => -1),
                array(
                    'children.products.children.0.children.quantity.errors.0'
                    =>
                    'Значение должно быть больше 0'
                )
            ),
            'zero quantity' => array(
                400,
                array('quantity' => 0),
                array(
                    'children.products.children.0.children.quantity.errors.0'
                    =>
                    'Значение должно быть больше 0'
                )
            ),
            'float quantity' => array(
                201,
                array('quantity' => 2.5),
            ),
            'float quantity with coma' => array(
                201,
                array('quantity' => '2,5'),
            ),
            'float quantity very float' => array(
                400,
                array('quantity' => 2.5555),
                array(
                    'children.products.children.0.children.quantity.errors.0'
                    =>
                    'Значение не должно содержать больше 3 цифр после запятой'
                )
            ),
            'float quantity very float with coma' => array(
                400,
                array('quantity' => '2,5555'),
                array(
                    'children.products.children.0.children.quantity.errors.0'
                    =>
                    'Значение не должно содержать больше 3 цифр после запятой',
                )
            ),
            'float quantity very float only one message' => array(
                400,
                array('quantity' => '2,5555'),
                array(
                    'children.products.children.0.children.quantity.errors.0'
                    =>
                    'Значение не должно содержать больше 3 цифр после запятой',
                    'children.products.children.0.children.quantity.errors.1'
                    =>
                    null
                )
            ),
            'not numeric quantity' => array(
                400,
                array('quantity' => 'abc'),
                array(
                    'children.products.children.0.children.quantity.errors.0'
                    =>
                    'Значение должно быть числом'
                )
            ),
            /***********************************************************************************************
             * 'price'
             ***********************************************************************************************/
            'valid price dot' => array(
                201,
                array('priceEntered' => 10.89),
            ),
            'valid price dot 79.99' => array(
                201,
                array('priceEntered' => 79.99),
            ),
            'valid price coma' => array(
                201,
                array('priceEntered' => '10,89'),
            ),
            'empty price' => array(
                400,
                array('priceEntered' => ''),
                array(
                    'children.products.children.0.children.priceEntered.errors.0'
                    =>
                    'Заполните это поле'
                )
            ),
            'not valid price very float' => array(
                400,
                array('priceEntered' => '10,898'),
                array(
                    'children.products.children.0.children.priceEntered.errors.0'
                    =>
                    'Цена не должна содержать больше 2 цифр после запятой'
                ),
            ),
            'not valid price very float dot' => array(
                400,
                array('priceEntered' => '10.898'),
                array(
                    'children.products.children.0.children.priceEntered.errors.0'
                    =>
                    'Цена не должна содержать больше 2 цифр после запятой'
                ),
            ),
            'valid price very float with dot' => array(
                201,
                array('priceEntered' => '10.12')
            ),
            'not valid price not a number' => array(
                400,
                array('priceEntered' => 'not a number'),
                array(
                    'children.products.children.0.children.priceEntered.errors.0'
                    =>
                    'Значение должно быть числом',
                ),
            ),
            'not valid price zero' => array(
                400,
                array('priceEntered' => 0),
                array(
                    'children.products.children.0.children.priceEntered.errors.0'
                    =>
                    'Цена не должна быть меньше или равна нулю'
                ),
            ),
            'not valid price negative' => array(
                400,
                array('priceEntered' => -10),
                array(
                    'children.products.children.0.children.priceEntered.errors.0'
                    =>
                    'Цена не должна быть меньше или равна нулю'
                )
            ),
            'not valid price too big 2 000 000 001' => array(
                400,
                array('priceEntered' => 2000000001),
                array(
                    'children.products.children.0.children.priceEntered.errors.0'
                    =>
                    'Цена не должна быть больше 10000000'
                ),
            ),
            'not valid price too big 100 000 000' => array(
                400,
                array('priceEntered' => '100000000'),
                array(
                    'children.products.children.0.children.priceEntered.errors.0'
                    =>
                    'Цена не должна быть больше 10000000'
                ),
            ),
            'valid price too big 10 000 000' => array(
                201,
                array('priceEntered' => '10000000'),
            ),
            'not valid price too big 10 000 001' => array(
                400,
                array('priceEntered' => '10000001'),
                array(
                    'children.products.children.0.children.priceEntered.errors.0'
                    =>
                    'Цена не должна быть больше 10000000'
                ),
            ),
            /***********************************************************************************************
             * 'product'
             ***********************************************************************************************/
            'not valid product' => array(
                400,
                array('product' => 'not_valid_product_id'),
                array(
                    'children.products.children.0.children.product.errors.0'
                    =>
                        'Такого товара не существует'
                ),
            ),
            'empty product' => array(
                400,
                array('product' => ''),
                array(
                    'children.products.children.0.children.product.errors.0'
                    =>
                    'Заполните это поле'
                ),
            ),
        );
    }

    public function testGetInvoiceProductsAction()
    {
        $store = $this->factory()->store()->getStore();
        $productId = $this->createProduct();
        $invoiceId = $this->createInvoice(array(), $store->id);
        $invoiceProduct1Id = $this->createInvoiceProduct($invoiceId, $productId, 10, 11.12);
        $invoiceProduct2Id = $this->createInvoiceProduct($invoiceId, $productId, 5, 12.76);
        $invoiceProduct3Id = $this->createInvoiceProduct($invoiceId, $productId, 1, 5.99);

        $accessToken = $this->factory()->oauth()->authAsDepartmentManager($store->id);

        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $store->id . '/invoices/' . $invoiceId . '/products'
        );

        Assert::assertJsonPathCount(3, '*.id', $response);
        Assert::assertJsonPathEquals($productId, '*.product.id', $response, 3);
        Assert::assertJsonPathEquals($invoiceProduct1Id, '0.id', $response, 1);
        Assert::assertJsonPathEquals($invoiceProduct2Id, '1.id', $response, 1);
        Assert::assertJsonPathEquals($invoiceProduct3Id, '2.id', $response, 1);
    }

    public function testGetInvoiceProductsActionNotFound()
    {
        $this->markTestSkipped();
        $accessToken = $this->factory->oauth()->auth($this->departmentManager);

        $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $this->storeId . '/invoices/123484923423/products'
        );

        $this->assertResponseCode(404);
    }

    public function testGetInvoiceProductNotFoundFromAnotherStore()
    {
        $this->markTestSkipped();
        $storeId2 = $this->factory->store()->getStoreId('43');
        $this->factory->store()->linkDepartmentManagers($this->departmentManager->id, $storeId2);

        $productId = $this->createProduct();
        $invoiceId = $this->createInvoice(array(), $this->storeId, $this->departmentManager);
        $invoiceProductId = $this->createInvoiceProduct(
            $invoiceId,
            $productId,
            2,
            19.25,
            $this->storeId,
            $this->departmentManager
        );

        $accessToken = $this->factory->oauth()->auth($this->departmentManager);

        $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $storeId2 . '/invoices/' . $invoiceId . '/products/' . $invoiceProductId
        );

        $this->assertResponseCode(404);

        $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $this->storeId . '/invoices/' . $invoiceId . '/products/' . $invoiceProductId
        );

        $this->assertResponseCode(200);
    }

    /**
     * @dataProvider putInvoiceProvider
     */
    public function testPutInvoiceProductAction($quantity, $price, $totalPrice, $newQuantity, $newPrice, $newTotalPrice)
    {
        $this->markTestSkipped();
        $invoiceId = $this->createInvoice(array(), $this->storeId, $this->departmentManager);
        $productId = $this->createProduct();

        $invoiceProductData = array(
            'quantity' => $quantity,
            'priceEntered'    => $price,
            'product'  => $productId,
        );

        $accessToken = $this->factory->oauth()->auth($this->departmentManager);

        $responseJson = $this->clientJsonRequest(
            $accessToken,
            'POST',
            '/api/1/stores/' . $this->storeId . '/invoices/' . $invoiceId . '/products',
            $invoiceProductData
        );

        Assert::assertResponseCode(201, $this->client->getResponse());
        Assert::assertJsonPathEquals($quantity, 'quantity', $responseJson);
        Assert::assertJsonPathEquals($price, 'price', $responseJson);
        Assert::assertJsonPathEquals($totalPrice, 'totalPrice', $responseJson);

        Assert::assertJsonPathEquals($productId, 'product.id', $responseJson);

        $this->assertStoreProductTotals($this->storeId, $productId, $quantity, $price);

        Assert::assertJsonPathEquals($invoiceId, 'invoice.id', $responseJson);
        Assert::assertJsonPathEquals(1, 'invoice.itemsCount', $responseJson);
        Assert::assertJsonPathEquals($totalPrice, 'invoice.sumTotal', $responseJson);

        $this->assertInvoiceTotals($invoiceId, $totalPrice, 1);

        $invoiceProductId = $responseJson['id'];

        $modifiedInvoiceProductData = array(
            'quantity' => $newQuantity,
            'priceEntered' => $newPrice,
        ) + $invoiceProductData;

        $responseJson = $this->clientJsonRequest(
            $accessToken,
            'PUT',
            '/api/1/stores/' . $this->storeId . '/invoices/' . $invoiceId . '/products/' . $invoiceProductId,
            $modifiedInvoiceProductData
        );

        Assert::assertResponseCode(200, $this->client->getResponse());
        Assert::assertJsonPathEquals($invoiceProductId, 'id', $responseJson);
        Assert::assertJsonPathEquals($newPrice, 'price', $responseJson);
        Assert::assertJsonPathEquals($newQuantity, 'quantity', $responseJson);
        Assert::assertJsonPathEquals($newTotalPrice, 'totalPrice', $responseJson);

        Assert::assertJsonPathEquals($productId, 'product.id', $responseJson);
        Assert::assertJsonPathEquals($invoiceId, 'invoice.id', $responseJson);

        $this->assertStoreProductTotals($this->storeId, $productId, $newQuantity, $newPrice);
        $this->assertInvoiceTotals($invoiceId, $newTotalPrice, 1);

        $responseJson = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $this->storeId . '/invoices/' . $invoiceId . '/products/' . $invoiceProductId
        );

        Assert::assertResponseCode(200, $this->client->getResponse());
        Assert::assertJsonPathEquals($invoiceProductId, 'id', $responseJson);
        Assert::assertJsonPathEquals($newPrice, 'price', $responseJson);
        Assert::assertJsonPathEquals($newQuantity, 'quantity', $responseJson);
        Assert::assertJsonPathEquals($newTotalPrice, 'totalPrice', $responseJson);

        Assert::assertJsonPathEquals($productId, 'product.id', $responseJson);
        Assert::assertJsonPathEquals($invoiceId, 'invoice.id', $responseJson);
    }

    /**
     * @return array
     */
    public function putInvoiceProvider()
    {
        return array(
            array(10, 5, 50, 20, 4, 80),
            array(6, 9.99, 59.94, 5, 9.99, 49.95)
        );
    }

    /**
     * @dataProvider putInvoiceProductActionChangeProductIdProvider
     */
    public function testPutInvoiceProductActionChangeProductId(
        $quantity1,
        $price1,
        $invoiceSumTotal1,
        $quantity2,
        $price2,
        $invoiceSumTotal2
    ) {
        $this->markTestSkipped();
        $invoiceId = $this->createInvoice(array(), $this->storeId, $this->departmentManager);
        $product1Id = $this->createProduct('_1');
        $product2Id = $this->createProduct('_2');

        $this->assertStoreProductTotals($this->storeId, $product1Id, 0, null);
        $this->assertStoreProductTotals($this->storeId, $product2Id, 0, null);

        // POST invoice product
        $postData = array(
            'quantity' => $quantity1,
            'priceEntered'    => $price1,
            'product'  => $product1Id,
        );

        $accessToken = $this->factory->oauth()->auth($this->departmentManager);

        $postJson = $this->clientJsonRequest(
            $accessToken,
            'POST',
            '/api/1/stores/' . $this->storeId . '/invoices/' . $invoiceId . '/products',
            $postData
        );

        $this->assertResponseCode(201);
        Assert::assertJsonHasPath('id', $postJson);
        $invoiceProductId = $postJson['id'];

        $this->assertStoreProductTotals($this->storeId, $product1Id, $quantity1, $price1);

        Assert::assertJsonPathEquals($invoiceSumTotal1, 'invoice.sumTotal', $postJson);
        Assert::assertJsonPathEquals(1, 'invoice.itemsCount', $postJson);

        $this->assertStoreProductTotals($this->storeId, $product1Id, $quantity1, $price1);
        $this->assertStoreProductTotals($this->storeId, $product2Id, 0, null);
        $this->assertInvoiceTotals($invoiceId, $invoiceSumTotal1, 1);

        // PUT invoice product with another product id
        $putData = array(
            'quantity' => $quantity2,
            'priceEntered' => $price2,
            'product'  => $product2Id,
        );

        $putJson = $this->clientJsonRequest(
            $accessToken,
            'PUT',
            '/api/1/stores/' . $this->storeId . '/invoices/' . $invoiceId . '/products/' . $invoiceProductId,
            $putData
        );

        $this->assertResponseCode(200);

        $this->assertStoreProductTotals($this->storeId, $product2Id, $quantity2, $price2);

        Assert::assertJsonPathEquals($invoiceSumTotal2, 'invoice.sumTotal', $putJson);
        Assert::assertJsonPathEquals(1, 'invoice.itemsCount', $putJson);

        $this->assertStoreProductTotals($this->storeId, $product1Id, 0, null);
        $this->assertStoreProductTotals($this->storeId, $product2Id, $quantity2, $price2);
        $this->assertInvoiceTotals($invoiceId, $invoiceSumTotal2, 1);
    }

    /**
     * @param string $invoiceId
     * @param string $sumTotal
     * @param int $itemsCount
     */
    protected function assertInvoiceTotals($invoiceId, $sumTotal, $itemsCount)
    {
        $accessToken = $this->factory->oauth()->auth($this->departmentManager);

        $invoiceJson = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $this->storeId . '/invoices/' . $invoiceId
        );

        $this->assertResponseCode(200);

        $assertions = array(
            'sumTotal' => $sumTotal,
            'itemsCount' => $itemsCount,
        );

        $this->performJsonAssertions($invoiceJson, $assertions);
    }

    /**
     * @return array
     */
    public function putInvoiceProductActionChangeProductIdProvider()
    {
        return array(
            'quantity and price changed' => array(
                10, // quantity of first product
                9.99, // price of first product
                99.9, // invoice sum total
                5, // quantity of second product
                5.99, // price of second product
                29.95, // invoice sum total
            ),
            'quantity and price are not changed' => array(
                10, // quantity of first product
                9.99, // price of first product
                99.9, // invoice sum total
                10, // quantity of second product
                9.99, // price of second product
                99.9, // invoice sum total
            ),
            'quantity changed' => array(
                10, // quantity of first product
                9.99, // price of first product
                99.9, // invoice sum total
                5, // quantity of second product
                9.99, // price of second product
                49.95, // invoice sum total
            ),
            'price changed' => array(
                10, // quantity of first product
                9.99, // price of first product
                99.9, // invoice sum total
                10, // quantity of second product
                5.99, // price of second product
                59.9, // invoice sum total
            ),
        );
    }

    public function testDeleteProductsAction()
    {
        $this->markTestSkipped();
        $store = $this->factory()->store()->getStore();
        $productId = $this->createProduct();
        $invoiceId = $this->createInvoice(array(), $store->id);

        $productsData = $this->createInvoiceProducts($productId, $invoiceId, $store->id);

        $accessToken = $this->factory->oauth()->auth($this->departmentManager);

        $this->clientJsonRequest(
            $accessToken,
            'DELETE',
            '/api/1/stores/' . $this->storeId . '/invoices/' . $invoiceId . '/products/' . $productsData[1]['id']
        );

        $this->assertResponseCode(204);

        $getResponse = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $this->storeId . '/invoices/' . $invoiceId . '/products'
        );

        $this->assertResponseCode(200);

        Assert::assertJsonPathCount(2, '*.id', $getResponse);

        Assert::assertJsonPathEquals($productsData[0]['id'], '*.id', $getResponse);
        Assert::assertJsonPathEquals($productsData[2]['id'], '*.id', $getResponse);

        Assert::assertNotJsonPathEquals($productsData[1]['id'], '*.id', $getResponse);
    }

    public function testDeleteProductsActionUpdateAmountAndInvoiceTotals()
    {
        $this->markTestSkipped();
        $store = $this->factory()->store()->getStore();
        $productId = $this->createProduct();
        $invoiceId = $this->createInvoice(array(), $store->id);

        $productsData = $this->createInvoiceProducts($productId, $invoiceId, $store->id);

        $accessToken = $this->factory->oauth()->auth($this->departmentManager);

        $this->assertStoreProductTotals($this->storeId, $productId, 16, 5.99);

        $this->assertInvoiceTotals($invoiceId, 180.99, 3);

        $this->clientJsonRequest(
            $accessToken,
            'DELETE',
            '/api/1/stores/' . $this->storeId . '/invoices/' . $invoiceId . '/products/' . $productsData[1]['id']
        );

        $this->assertResponseCode(204);

        $getResponse = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $this->storeId . '/invoices/' . $invoiceId . '/products'
        );

        $this->assertResponseCode(200);

        Assert::assertJsonPathCount(2, '*.id', $getResponse);

        Assert::assertJsonPathEquals($productsData[0]['id'], '*.id', $getResponse);
        Assert::assertJsonPathEquals($productsData[2]['id'], '*.id', $getResponse);

        Assert::assertNotJsonPathEquals($productsData[1]['id'], '*.id', $getResponse);

        $this->assertStoreProductTotals($this->storeId, $productId, 11, $productsData[2]['priceEntered']);
        $this->assertInvoiceTotals($invoiceId, 117.19, 2);
    }

    public function testLastPurchasePriceChangeOnInvoiceProductDelete()
    {
        $this->markTestSkipped();
        $productId = $this->createProduct();

        $invoiceId = $this->createInvoice(array(), $this->storeId, $this->departmentManager);

        $invoiceProducts = $this->createInvoiceProducts($productId, $invoiceId);

        $this->assertStoreProductTotals(
            $this->storeId,
            $productId,
            $invoiceProducts[2]['productAmount'],
            $invoiceProducts[2]['priceEntered']
        );

        $accessToken = $this->factory->oauth()->auth($this->departmentManager);

        $this->clientJsonRequest(
            $accessToken,
            'DELETE',
            '/api/1/stores/' . $this->storeId . '/invoices/' . $invoiceId . '/products/' . $invoiceProducts[2]['id']
        );

        $this->assertResponseCode(204);

        $this->assertStoreProductTotals($this->storeId, $productId, 15, $invoiceProducts[1]['priceEntered']);

        $this->clientJsonRequest(
            $accessToken,
            'DELETE',
            '/api/1/stores/' . $this->storeId . '/invoices/' . $invoiceId . '/products/' . $invoiceProducts[0]['id']
        );

        $this->assertResponseCode(204);

        $this->assertStoreProductTotals($this->storeId, $productId, 5, $invoiceProducts[1]['priceEntered']);

        $this->clientJsonRequest(
            $accessToken,
            'DELETE',
            '/api/1/stores/' . $this->storeId . '/invoices/' . $invoiceId . '/products/' . $invoiceProducts[1]['id']
        );

        $this->assertResponseCode(204);

        $this->assertStoreProductTotals($this->storeId, $productId, 0, null);
    }

    public function testLastPurchasePriceChangeOnInvoiceProductUpdate()
    {
        $this->markTestSkipped();
        $productId = $this->createProduct();

        $invoiceId = $this->createInvoice(array(), $this->storeId, $this->departmentManager);

        $invoiceProducts = $this->createInvoiceProducts($productId, $invoiceId);

        $this->assertStoreProductTotals(
            $this->storeId,
            $productId,
            $invoiceProducts[2]['productAmount'],
            $invoiceProducts[2]['priceEntered']
        );

        $newInvoiceProductData = $invoiceProducts[1];
        $newInvoiceProductData['priceEntered'] = 13.01;
        unset($newInvoiceProductData['productAmount'], $newInvoiceProductData['id']);

        $accessToken = $this->factory->oauth()->auth($this->departmentManager);

        $this->clientJsonRequest(
            $accessToken,
            'PUT',
            '/api/1/stores/' . $this->storeId . '/invoices/' . $invoiceId . '/products/' . $invoiceProducts[1]['id'],
            $newInvoiceProductData
        );

        $this->assertResponseCode(200);

        $this->assertStoreProductTotals($this->storeId, $productId, 16, $invoiceProducts[2]['priceEntered']);

        $newProductId = $this->createProduct('NEW');
        $newInvoiceProductDataNewProduct = $invoiceProducts[2];
        $newInvoiceProductDataNewProduct['product'] = $newProductId;

        unset($newInvoiceProductDataNewProduct['productAmount'], $newInvoiceProductDataNewProduct['id']);

        $this->clientJsonRequest(
            $accessToken,
            'PUT',
            '/api/1/stores/' . $this->storeId . '/invoices/' . $invoiceId . '/products/' . $invoiceProducts[2]['id'],
            $newInvoiceProductDataNewProduct
        );

        $this->assertResponseCode(200);

        $this->assertStoreProductTotals($this->storeId, $productId, 15, $newInvoiceProductData['priceEntered']);
        $this->assertStoreProductTotals(
            $this->storeId,
            $newProductId,
            1,
            $newInvoiceProductDataNewProduct['priceEntered']
        );
    }

    public function testAveragePurchasePrice()
    {
        $this->markTestSkipped();
        $productId1 = $this->createProduct('1');
        $productId2 = $this->createProduct('2');

        $invoiceId1 = $this->createInvoice(
            array(
                'acceptanceDate' => date('c', strtotime('-3 days'))
            ),
            $this->storeId,
            $this->departmentManager
        );

        $invoiceIdOld = $this->createInvoice(
            array(
                'acceptanceDate' => date('c', strtotime('-31 days'))
            ),
            $this->storeId,
            $this->departmentManager
        );
        $this->createInvoiceProduct($invoiceIdOld, $productId1, 10, 23.33);

        $invoiceProductId1 = $this->createInvoiceProduct($invoiceId1, $productId1, 10, 26);
        $this->createInvoiceProduct($invoiceId1, $productId2, 6, 34.67);

        /* @var $averagePriceService StoreProductMetricsCalculator */
        $averagePriceService = $this->getContainer()->get('lighthouse.core.service.product.metrics_calculator');
        $averagePriceService->recalculateAveragePrice();

        $this->assertStoreProduct($this->storeId, $productId1, array('averagePurchasePrice' => 26));

        $invoiceId2 = $this->createInvoice(
            array(
                'acceptanceDate' => date('c', strtotime('-2 days'))
            ),
            $this->storeId,
            $this->departmentManager
        );

        $invoiceProductId2 = $this->createInvoiceProduct($invoiceId2, $productId1, 5, 29);

        $averagePriceService->recalculateAveragePrice();

        $this->assertStoreProduct($this->storeId, $productId1, array('averagePurchasePrice' => 27));

        $invoiceId3 = $this->createInvoice(
            array(
                'acceptanceDate' => date('c', strtotime('-1 days'))
            ),
            $this->storeId,
            $this->departmentManager
        );

        $invoiceProductId3 = $this->createInvoiceProduct($invoiceId3, $productId1, 10, 31);

        $averagePriceService->recalculateAveragePrice();

        $this->assertStoreProduct($this->storeId, $productId1, array('averagePurchasePrice' => 28.6));

        $accessToken = $this->factory->oauth()->auth($this->departmentManager);

        $this->clientJsonRequest(
            $accessToken,
            'DELETE',
            '/api/1/stores/' . $this->storeId . '/invoices/' . $invoiceId3 . '/products/' . $invoiceProductId3
        );

        $this->assertResponseCode(204);

        $averagePriceService->recalculateAveragePrice();

        $this->assertStoreProduct(
            $this->storeId,
            $productId1,
            array('averagePurchasePrice' => 27, 'lastPurchasePrice' => 29)
        );

        $this->clientJsonRequest(
            $accessToken,
            'DELETE',
            '/api/1/stores/' . $this->storeId . '/invoices/' . $invoiceId2 . '/products/' . $invoiceProductId2
        );

        $this->assertResponseCode(204);

        $averagePriceService->recalculateAveragePrice();

        $this->assertStoreProduct(
            $this->storeId,
            $productId1,
            array('averagePurchasePrice' => 26, 'lastPurchasePrice' => 26)
        );

        $this->clientJsonRequest(
            $accessToken,
            'DELETE',
            '/api/1/stores/' . $this->storeId . '/invoices/' . $invoiceId1. '/products/' . $invoiceProductId1
        );

        $this->assertResponseCode(204);

        $averagePriceService->recalculateAveragePrice();

        $this->assertStoreProduct(
            $this->storeId,
            $productId1,
            array('averagePurchasePrice' => null, 'lastPurchasePrice' => 23.33)
        );
    }

    public function testAveragePurchasePriceRounded()
    {
        $store = $this->factory()->store()->getStore();

        $productId = $this->createProduct();
        $productId2 = $this->createProduct('2');

        $invoiceId1 = $this->createInvoice(array('acceptanceDate' => date('c', strtotime('-3 days'))), $store->id);

        $invoiceIdOld = $this->createInvoice(array('acceptanceDate' => date('c', strtotime('-15 days'))), $store->id);

        $this->createInvoiceProduct($invoiceIdOld, $productId, 10, 23.33);
        $this->createInvoiceProduct($invoiceId1, $productId, 10, 26);
        $this->createInvoiceProduct($invoiceId1, $productId2, 6, 34.67);

        /* @var $averagePriceService StoreProductMetricsCalculator */
        $averagePriceService = $this->getContainer()->get('lighthouse.core.service.product.metrics_calculator');
        $averagePriceService->recalculateAveragePrice();

        $this->assertStoreProduct($store->id, $productId, array('averagePurchasePrice' => 24.67));
    }

    public function testAveragePurchasePriceChangeOnInvoiceDateChange()
    {
        $this->markTestSkipped();
        $store = $this->factory()->store()->getStore();
        $productId = $this->createProduct();

        $invoiceData = array(
            'acceptanceDate' => date('c', strtotime('now')),
            'accepter' => 'Приемных Н.П.',
            'legalEntity' => 'ООО "Магазин"',
            'supplierInvoiceNumber' => '1248373',
            'includesVAT' => true,
        );

        $invoiceId = $this->createInvoice($invoiceData, $store->id);

        $this->createInvoiceProduct($invoiceId, $productId, 10, 26);

        /* @var $averagePriceService StoreProductMetricsCalculator */
        $averagePriceService = $this->getContainer()->get('lighthouse.core.service.product.metrics_calculator');
        $averagePriceService->recalculateAveragePrice();

        $this->assertStoreProduct(
            $store->id,
            $productId,
            array(
                'averagePurchasePrice' => null,
                'lastPurchasePrice' => 26
            )
        );

        $accessToken = $this->factory->oauth()->authAsDepartmentManager($store->id);

        $this->clientJsonRequest(
            $accessToken,
            'PUT',
            '/api/1/stores/' . $store->id . '/invoices/' . $invoiceId,
            array(
                'acceptanceDate' => date('c', strtotime('-2 days 13:00'))
            ) + $invoiceData
        );

        $this->assertResponseCode(200);

        $averagePriceService->recalculateAveragePrice();

        $this->assertStoreProduct(
            $store->id,
            $productId,
            array(
                'averagePurchasePrice' => 26,
                'lastPurchasePrice' => 26
            )
        );
    }

    public function testProductDataDoesNotChangeInInvoiceAfterProductUpdate()
    {
        $this->markTestSkipped();
        $productId = $this->createProduct(array('name' => 'Кефир 1%', 'sku' => 'кефир_1%'));

        $this->assertProduct($productId, array('name' => 'Кефир 1%', 'sku' => 'кефир_1%'));

        $invoiceId = $this->createInvoice(array(), $this->storeId, $this->departmentManager);
        $this->createInvoiceProduct($invoiceId, $productId, 10, 10.12);

        $accessToken = $this->factory->oauth()->auth($this->departmentManager);

        $invoiceProductsResponse = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $this->storeId . '/invoices/' . $invoiceId . '/products'
        );

        $this->assertResponseCode(200);

        Assert::assertJsonPathEquals($productId, '0.product.id', $invoiceProductsResponse);
        Assert::assertJsonPathEquals('Кефир 1%', '0.product.name', $invoiceProductsResponse);
        Assert::assertJsonPathEquals('кефир_1%', '0.product.sku', $invoiceProductsResponse);

        $this->updateProduct($productId, array('name' => 'Кефир 5%', 'sku' => 'кефир_5%'));

        $invoiceProductsResponse = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $this->storeId . '/invoices/' . $invoiceId . '/products'
        );

        $this->assertResponseCode(200);

        Assert::assertJsonPathEquals($productId, '0.product.id', $invoiceProductsResponse);
        Assert::assertJsonPathEquals('Кефир 1%', '0.product.name', $invoiceProductsResponse);
        Assert::assertJsonPathEquals('кефир_1%', '0.product.sku', $invoiceProductsResponse);

        $this->assertProduct($productId, array('name' => 'Кефир 5%', 'sku' => 'кефир_5%'));
    }

    public function testTwoProductVersionsInInvoice()
    {
        $this->markTestSkipped();
        $productId = $this->createProduct(array('name' => 'Кефир 1%', 'sku' => 'кефир_1%'));
        $invoiceId = $this->createInvoice(array(), $this->storeId, $this->departmentManager);
        $this->createInvoiceProduct($invoiceId, $productId, 10, 10.12);

        $this->updateProduct($productId, array('name' => 'Кефир 5%', 'sku' => 'кефир_5%'));

        $this->factory->clear();
        $this->createInvoiceProduct($invoiceId, $productId, 10, 10.12);

        $accessToken = $this->factory->oauth()->auth($this->departmentManager);

        $invoiceProductsResponse = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $this->storeId . '/invoices/' . $invoiceId . '/products'
        );

        $this->assertResponseCode(200);

        Assert::assertJsonPathCount(2, '*.id', $invoiceProductsResponse);
        Assert::assertJsonPathEquals($productId, '*.product.id', $invoiceProductsResponse, 2);
        Assert::assertJsonPathEquals('Кефир 1%', '*.product.name', $invoiceProductsResponse, 1);
        Assert::assertJsonPathEquals('кефир_1%', '*.product.sku', $invoiceProductsResponse, 1);
        Assert::assertJsonPathEquals('Кефир 5%', '*.product.name', $invoiceProductsResponse, 1);
        Assert::assertJsonPathEquals('кефир_5%', '*.product.sku', $invoiceProductsResponse, 1);
    }

    public function testTwoProductVersionsCreated()
    {
        $this->markTestSkipped();
        $productId = $this->createProduct(array('name' => 'Кефир 1%', 'sku' => 'кефир_1%'));
        $invoiceId = $this->createInvoice(array(), $this->storeId, $this->departmentManager);

        $this->createInvoiceProduct($invoiceId, $productId, 10, 10.12);

        $this->updateProduct($productId, array('name' => 'Кефир 5%', 'sku' => 'кефир_5%'));

        $this->factory->clear();
        $this->createInvoiceProduct($invoiceId, $productId, 10, 10.12);

        /* @var VersionRepository $productVersionRepository */
        $productVersionRepository = $this->getContainer()->get('lighthouse.core.document.repository.product_version');

        $productVersions = $productVersionRepository->findAllByDocumentId($productId);

        $this->assertCount(2, $productVersions);

        $firstProduct = $productVersions->getNext();
        $secondProduct = $productVersions->getNext();

        $this->assertEquals('Кефир 5%', $firstProduct->name);
        $this->assertEquals('Кефир 1%', $secondProduct->name);
    }

    public function testGetProductInvoiceProducts()
    {
        $this->markTestSkipped();
        $productId1 = $this->createProduct(array('name' => 'Кефир 1%', 'sku' => 'кефир_1%', 'purchasePrice' => 35.24));
        $productId2 = $this->createProduct(array('name' => 'Кефир 5%', 'sku' => 'кефир_5%', 'purchasePrice' => 35.64));
        $productId3 = $this->createProduct(array('name' => 'Кефир 0%', 'sku' => 'кефир_0%', 'purchasePrice' => 42.15));

        $invoiceId1 = $this->createInvoice(
            array(
                'acceptanceDate' => '2013-10-18T09:39:47+0400'
            ),
            $this->storeId,
            $this->departmentManager
        );
        $invoiceProductId11 = $this->createInvoiceProduct($invoiceId1, $productId1, 100, 36.70);
        $invoiceProductId13 = $this->createInvoiceProduct($invoiceId1, $productId3, 20, 42.90);

        $invoiceId2 = $this->createInvoice(
            array(
                'acceptanceDate' => '2013-10-18T12:22:00+0400'
            ),
            $this->storeId,
            $this->departmentManager
        );
        $invoiceProductId21 = $this->createInvoiceProduct($invoiceId2, $productId1, 120, 37.20);
        $invoiceProductId22 = $this->createInvoiceProduct($invoiceId2, $productId2, 200, 35.80);

        $accessToken = $this->factory->oauth()->auth($this->departmentManager);
        $getResponse = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $this->storeId . '/products/' . $productId1 . '/invoiceProducts'
        );

        $this->assertResponseCode(200);

        Assert::assertJsonPathCount(2, '*.id', $getResponse);
        Assert::assertNotJsonPathEquals($invoiceProductId13, '*.id', $getResponse);
        Assert::assertNotJsonPathEquals($invoiceProductId22, '*.id', $getResponse);
        Assert::assertJsonPathEquals($invoiceProductId11, '1.id', $getResponse);
        Assert::assertJsonPathEquals($invoiceProductId21, '0.id', $getResponse);
        Assert::assertJsonPathEquals($invoiceId1, '1.invoice.id', $getResponse);
        Assert::assertJsonPathEquals($invoiceId2, '0.invoice.id', $getResponse);
        Assert::assertNotJsonHasPath('*.store', $getResponse);
        Assert::assertNotJsonHasPath('*.originalProduct', $getResponse);

        Assert::assertJsonHasPath('0.acceptanceDate', $getResponse);
        Assert::assertJsonHasPath('0.invoice.acceptanceDate', $getResponse);
        $this->assertEquals($getResponse[0]['acceptanceDate'], $getResponse[0]['invoice']['acceptanceDate']);

        Assert::assertJsonHasPath('1.acceptanceDate', $getResponse);
        Assert::assertJsonHasPath('1.invoice.acceptanceDate', $getResponse);
        $this->assertEquals($getResponse[1]['acceptanceDate'], $getResponse[1]['invoice']['acceptanceDate']);
    }

    public function testInvoiceProductTotalPriceWithFloatQuantity()
    {
        $this->markTestSkipped();
        $productId1 = $this->createProduct(array('name' => 'Кефир 1%', 'sku' => 'кефир_1%', 'purchasePrice' => 35.24));
        $productId2 = $this->createProduct(array('name' => 'Кефир 5%', 'sku' => 'кефир_5%', 'purchasePrice' => 35.64));
        $productId3 = $this->createProduct(array('name' => 'Кефир 0%', 'sku' => 'кефир_0%', 'purchasePrice' => 42.15));

        $invoiceId1 = $this->createInvoice(
            array(
                'acceptanceDate' => '2013-10-18T09:39:47+0400'
            ),
            $this->storeId,
            $this->departmentManager
        );

        $this->createInvoiceProduct($invoiceId1, $productId1, 99.99, 36.78);
        $this->createInvoiceProduct($invoiceId1, $productId2, 0.4, 21.77);
        $this->createInvoiceProduct($invoiceId1, $productId3, 7.77, 42.99);

        $accessToken = $this->factory->oauth()->auth($this->departmentManager);
        $getResponse = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $this->storeId . '/products/' . $productId1 . '/invoiceProducts'
        );

        $this->assertResponseCode(200);
        Assert::assertJsonPathCount(1, '*.id', $getResponse);
        Assert::assertJsonPathEquals(3677.63, "*.totalPrice", $getResponse);
        Assert::assertJsonPathEquals(36.78, "*.price", $getResponse);
        Assert::assertJsonPathEquals(99.99, "*.quantity", $getResponse);


        $getResponse = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $this->storeId . '/products/' . $productId2 . '/invoiceProducts'
        );

        $this->assertResponseCode(200);
        Assert::assertJsonPathCount(1, '*.id', $getResponse);
        Assert::assertJsonPathEquals(8.71, "*.totalPrice", $getResponse);
        Assert::assertJsonPathEquals(0.4, "*.quantity", $getResponse);
        Assert::assertJsonPathEquals(21.77, "*.price", $getResponse);


        $getResponse = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $this->storeId . '/products/' . $productId3 . '/invoiceProducts'
        );

        $this->assertResponseCode(200);
        Assert::assertJsonPathCount(1, '*.id', $getResponse);
        Assert::assertJsonPathEquals(334.03, "*.totalPrice", $getResponse);
        Assert::assertJsonPathEquals(42.99, "*.price", $getResponse);
        Assert::assertJsonPathEquals(7.77, "*.quantity", $getResponse);
    }

    public function testInvoiceProductWithVATFields()
    {
        $this->markTestSkipped();
        $productId1 = $this->createProduct(array('name' => 'Кефир 1%', 'sku' => 'кефир_1%', 'purchasePrice' => 35.24));

        $invoiceId1 = $this->createInvoice(
            array(
                'acceptanceDate' => '2013-10-18T09:39:47+0400',
                'includesVAT' => true,
            ),
            $this->storeId,
            $this->departmentManager
        );

        $this->createInvoiceProduct($invoiceId1, $productId1, 99.99, 36.78);

        $accessToken = $this->factory->oauth()->auth($this->departmentManager);
        $getResponse = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $this->storeId . '/products/' . $productId1 . '/invoiceProducts'
        );

        $this->assertResponseCode(200);
        Assert::assertJsonPathCount(1, '*.id', $getResponse);
        Assert::assertJsonPathEquals(3677.63, "0.totalPrice", $getResponse);
        Assert::assertJsonPathEquals(3343.66, "0.totalPriceWithoutVAT", $getResponse);
        Assert::assertJsonPathEquals(333.97, "0.totalAmountVAT", $getResponse);
        Assert::assertJsonPathEquals(36.78, "0.priceEntered", $getResponse);
        Assert::assertJsonPathEquals(36.78, "0.price", $getResponse);
        Assert::assertJsonPathEquals(33.44, "0.priceWithoutVAT", $getResponse);
        Assert::assertJsonPathEquals(3.34, "0.amountVAT", $getResponse);
        Assert::assertJsonPathEquals(99.99, "0.quantity", $getResponse);
    }

    /**
     * Проверяем что указав цену без НДС получим данные соответствующие данным теста выше
     */
    public function testInvoiceProductWithVATFieldsWithoutVAT()
    {
        $this->markTestSkipped();
        $productId1 = $this->createProduct(array('name' => 'Кефир 1%', 'sku' => 'кефир_1%', 'purchasePrice' => 35.24));

        $invoiceId1 = $this->createInvoice(
            array(
                'acceptanceDate' => '2013-10-18T09:39:47+0400',
                'includesVAT' => false,
            ),
            $this->storeId,
            $this->departmentManager
        );

        $this->createInvoiceProduct($invoiceId1, $productId1, 99.99, 33.44, null, null, false);

        $accessToken = $this->factory->oauth()->auth($this->departmentManager);
        $getResponse = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $this->storeId . '/products/' . $productId1 . '/invoiceProducts'
        );

        $this->assertResponseCode(200);
        Assert::assertJsonPathCount(1, '*.id', $getResponse);
        Assert::assertJsonPathEquals(3677.63, "0.totalPrice", $getResponse);
        Assert::assertJsonPathEquals(3343.66, "0.totalPriceWithoutVAT", $getResponse);
        Assert::assertJsonPathEquals(333.97, "0.totalAmountVAT", $getResponse);
        Assert::assertJsonPathEquals(33.44, "0.priceEntered", $getResponse);
        Assert::assertJsonPathEquals(36.78, "0.price", $getResponse);
        Assert::assertJsonPathEquals(33.44, "0.priceWithoutVAT", $getResponse);
        Assert::assertJsonPathEquals(3.34, "0.amountVAT", $getResponse);
        Assert::assertJsonPathEquals(99.99, "0.quantity", $getResponse);
    }

    public function testInvoiceProductVATFieldChangeIncludesVATInInvoice()
    {
        $this->markTestSkipped();
        $productId1 = $this->createProduct(array('name' => 'Кефир 1%', 'sku' => 'кефир_1%', 'purchasePrice' => 35.24));

        $invoiceData = array(
            'supplier' => 'ООО "Поставщик"',
            'acceptanceDate' => '2013-03-18 12:56',
            'accepter' => 'Приемных Н.П.',
            'legalEntity' => 'ООО "Магазин"',
            'supplierInvoiceNumber' => '1248373',
            'includesVAT' => true,
        );

        $invoiceId1 = $this->createInvoice(
            $invoiceData,
            $this->storeId,
            $this->departmentManager
        );

        $this->createInvoiceProduct($invoiceId1, $productId1, 99.99, 36.78);

        $accessToken = $this->factory->oauth()->auth($this->departmentManager);
        $getResponse = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $this->storeId . '/products/' . $productId1 . '/invoiceProducts'
        );

        $this->assertResponseCode(200);
        Assert::assertJsonPathCount(1, '*.id', $getResponse);
        Assert::assertJsonPathEquals(3677.63, "0.totalPrice", $getResponse);
        Assert::assertJsonPathEquals(3343.66, "0.totalPriceWithoutVAT", $getResponse);
        Assert::assertJsonPathEquals(333.97, "0.totalAmountVAT", $getResponse);
        Assert::assertJsonPathEquals(36.78, "0.priceEntered", $getResponse);
        Assert::assertJsonPathEquals(36.78, "0.price", $getResponse);
        Assert::assertJsonPathEquals(33.44, "0.priceWithoutVAT", $getResponse);
        Assert::assertJsonPathEquals(3.34, "0.amountVAT", $getResponse);
        Assert::assertJsonPathEquals(99.99, "0.quantity", $getResponse);


        $invoiceData['includesVAT'] = false;

        $invoiceResponse = $this->clientJsonRequest(
            $accessToken,
            'PUT',
            '/api/1/stores/' . $this->storeId . '/invoices/' . $invoiceId1,
            $invoiceData
        );
        $this->assertResponseCode(200);

        Assert::assertJsonPathEquals(false, 'includesVAT', $invoiceResponse);
        Assert::assertJsonPathEquals(4045.60, 'sumTotal', $invoiceResponse);
        Assert::assertJsonPathEquals(3677.63, 'sumTotalWithoutVAT', $invoiceResponse);
        Assert::assertJsonPathEquals(367.96, 'totalAmountVAT', $invoiceResponse);

        $getResponse = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $this->storeId . '/products/' . $productId1 . '/invoiceProducts'
        );

        $this->assertResponseCode(200);
        Assert::assertJsonPathCount(1, '*.id', $getResponse);
        Assert::assertJsonPathEquals(4045.60, "0.totalPrice", $getResponse);
        Assert::assertJsonPathEquals(3677.63, "0.totalPriceWithoutVAT", $getResponse);
        Assert::assertJsonPathEquals(367.96, "0.totalAmountVAT", $getResponse);
        Assert::assertJsonPathEquals(36.78, "0.priceEntered", $getResponse);
        Assert::assertJsonPathEquals(40.46, "0.price", $getResponse);
        Assert::assertJsonPathEquals(36.78, "0.priceWithoutVAT", $getResponse);
        Assert::assertJsonPathEquals(3.68, "0.amountVAT", $getResponse);
        Assert::assertJsonPathEquals(99.99, "0.quantity", $getResponse);



        $invoiceData['includesVAT'] = true;

        $invoiceResponse = $this->clientJsonRequest(
            $accessToken,
            'PUT',
            '/api/1/stores/' . $this->storeId . '/invoices/' . $invoiceId1,
            $invoiceData
        );
        $this->assertResponseCode(200);

        Assert::assertJsonPathEquals(true, 'includesVAT', $invoiceResponse);
        Assert::assertJsonPathEquals(3677.63, 'sumTotal', $invoiceResponse);
        Assert::assertJsonPathEquals(3343.66, 'sumTotalWithoutVAT', $invoiceResponse);
        Assert::assertJsonPathEquals(333.97, 'totalAmountVAT', $invoiceResponse);


        $getResponse = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $this->storeId . '/products/' . $productId1 . '/invoiceProducts'
        );

        $this->assertResponseCode(200);
        Assert::assertJsonPathCount(1, '*.id', $getResponse);
        Assert::assertJsonPathEquals(3677.63, "0.totalPrice", $getResponse);
        Assert::assertJsonPathEquals(3343.66, "0.totalPriceWithoutVAT", $getResponse);
        Assert::assertJsonPathEquals(333.97, "0.totalAmountVAT", $getResponse);
        Assert::assertJsonPathEquals(36.78, "0.priceEntered", $getResponse);
        Assert::assertJsonPathEquals(36.78, "0.price", $getResponse);
        Assert::assertJsonPathEquals(33.44, "0.priceWithoutVAT", $getResponse);
        Assert::assertJsonPathEquals(3.34, "0.amountVAT", $getResponse);
        Assert::assertJsonPathEquals(99.99, "0.quantity", $getResponse);
    }

    public function testPutWithEmptyQuantity()
    {
        $storeId = $this->factory->store()->getStoreId();
        $productId = $this->createProduct();
        $invoiceId = $this->createInvoice(array(), $storeId);
        $invoiceProductId = $this->createInvoiceProduct($invoiceId, $productId, 1, 9.99, $storeId);

        $accessToken = $this->factory->oauth()->authAsDepartmentManager($storeId);

        $response = $this->clientJsonRequest(
            $accessToken,
            'PUT',
            '/api/1/stores/' . $storeId . '/invoices/' . $invoiceId . '/products/' . $invoiceProductId,
            array(
                'product' => $productId,
                'priceEntered' => 9.99,
                'quantity' => ''
            )
        );

        $this->assertResponseCode(400);
        Assert::assertJsonPathEquals('Заполните это поле', 'children.quantity.errors.0', $response);
    }

    public function testGetInvoiceProductAfterEditInvoiceAcceptanceDate()
    {
        $storeId = $this->factory()->store()->getStoreId();
        $productId = $this->createProduct();
        $invoiceId = $this->createInvoice(array('acceptanceDate' => '2014-01-10T12:33:33+0400'), $storeId);
        $this->createInvoiceProduct($invoiceId, $productId, 1, 9.99, $storeId);

        $accessToken = $this->factory()->oauth()->authAsDepartmentManager($storeId);

        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $storeId . '/invoices/' . $invoiceId . '/products'
        );

        $this->assertResponseCode(200);
        Assert::assertJsonPathEquals('2014-01-10T12:33:33+0400', '0.acceptanceDate', $response);


        $this->editInvoice(array('acceptanceDate' => '2014-01-03T10:11:10+0400'), $invoiceId, $storeId);

        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $storeId . '/invoices/' . $invoiceId
        );

        $this->assertResponseCode(200);
        Assert::assertJsonPathEquals('2014-01-03T10:11:10+0400', 'acceptanceDate', $response);
        Assert::assertJsonPathEquals('2014-01-03T10:11:10+0400', 'products.0.acceptanceDate', $response);
    }

    public function testProductsActionProductCategoryIsNotExposed()
    {
        $storeId = $this->factory()->store()->getStoreId();

        $productId1 = $this->createProduct('1');
        $productId2 = $this->createProduct('2');
        $productId3 = $this->createProduct('3');

        $invoiceId = $this->createInvoice(array(), $storeId);

        $this->createInvoiceProduct($invoiceId, $productId1, 2, 9.99, $storeId);
        $this->createInvoiceProduct($invoiceId, $productId2, 3, 4.99, $storeId);
        $this->createInvoiceProduct($invoiceId, $productId3, 2, 1.95, $storeId);

        $accessToken = $this->factory->oauth()->authAsDepartmentManager($storeId);
        $getResponse = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $storeId .'/invoices/' . $invoiceId . '/products'
        );

        $this->assertResponseCode(200);
        Assert::assertJsonHasPath('*.product.subCategory', $getResponse);
        Assert::assertJsonHasPath('*.invoice', $getResponse);
        Assert::assertNotJsonHasPath('*.product.subCategory.category.group', $getResponse);
        Assert::assertJsonPathCount(0, '*.invoice.products.*.id', $getResponse);
        Assert::assertNotJsonHasPath('*.product.subCategory.category', $getResponse);
    }
}
