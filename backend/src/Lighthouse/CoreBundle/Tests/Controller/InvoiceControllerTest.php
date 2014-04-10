<?php

namespace Lighthouse\CoreBundle\Tests\Controller;

use Lighthouse\CoreBundle\Test\Assert;
use Lighthouse\CoreBundle\Test\WebTestCase;

class InvoiceControllerTest extends WebTestCase
{
    /**
     * @dataProvider postInvoiceDataProvider
     */
    public function testPostInvoiceAction(array $invoiceData, array $assertions = array())
    {
        $store = $this->factory()->store()->getStore();
        $supplier = $this->factory()->supplier()->getSupplier('ООО "Поставщик"');
        $invoiceData['supplier'] = $supplier->id;

        $accessToken = $this->factory->oauth()->authAsDepartmentManager($store->id);

        $postResponse = $this->clientJsonRequest(
            $accessToken,
            'POST',
            '/api/1/stores/' . $store->id . '/invoices',
            $invoiceData
        );

        $this->assertResponseCode(201);

        $assertions['supplier.id'] = $supplier->id;
        $assertions['supplier.name'] = 'ООО "Поставщик"';

        $this->performJsonAssertions($postResponse, $assertions, true);
    }

    /**
     * @dataProvider postInvoiceDataProvider
     */
    public function testGetInvoicesAction(array $invoiceData)
    {
        $store = $this->factory()->store()->getStore();
        for ($i = 0; $i < 5; $i++) {
            $this->createInvoice($invoiceData, $store->id);
        }

        $accessToken = $this->factory->oauth()->authAsDepartmentManager($store->id);

        $getResponse = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $store->id . '/invoices'
        );

        $this->assertResponseCode(200);
        Assert::assertJsonPathCount(5, '*.id', $getResponse);
    }

    public function testGetInvoicesActionMaxDepth()
    {
        $store = $this->factory->store()->getStore();
        $products = $this->createProductsBySku(array('1', '2', '3'));

        $invoiceId1 = $this->createInvoice(array(), $store->id);
        $this->createInvoiceProduct($invoiceId1, $products['1'], 9, 9.99);
        $this->createInvoiceProduct($invoiceId1, $products['2'], 19, 19.99);

        $invoiceId2 = $this->createInvoice(array(), $store->id);
        $this->createInvoiceProduct($invoiceId2, $products['1'], 119, 9.99);
        $this->createInvoiceProduct($invoiceId2, $products['2'], 129, 19.99);
        $this->createInvoiceProduct($invoiceId2, $products['3'], 139, 19.99);

        $accessToken = $this->factory->oauth()->authAsDepartmentManager($store->id);

        $getResponse = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $store->id . '/invoices'
        );

        $this->assertResponseCode(200);
        Assert::assertJsonPathCount(0, '0.store.departmentManagers.*', $getResponse);
        Assert::assertJsonPathCount(0, '0.store.storeManagers.*', $getResponse);
        Assert::assertJsonHasPath('0.products.0.product', $getResponse);
        Assert::assertJsonHasPath('0.products.1.product', $getResponse);
        Assert::assertNotJsonHasPath('0.products.0.product.subCategory', $getResponse);
        Assert::assertNotJsonHasPath('0.products.1.product.subCategory', $getResponse);
    }

    /**
     * @dataProvider postInvoiceDataProvider
     */
    public function testGetInvoice(array $invoiceData, array $assertions)
    {
        $store = $this->factory()->store()->getStore();
        $invoice = $this->factory()->invoice()->createInvoice($invoiceData, $store->id);

        $accessToken = $this->factory->oauth()->authAsDepartmentManager($store->id);

        $getResponse = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $store->id . '/invoices/' . $invoice->id
        );

        $this->assertResponseCode(200);

        $this->performJsonAssertions($getResponse, $assertions, true);
    }

    public function postInvoiceDataProvider()
    {
        return array(
            'invoice' => array(
                'data' => array(
                    'acceptanceDate' => '2013-03-18 12:56',
                    'accepter' => 'Приемных Н.П.',
                    'legalEntity' => 'ООО "Магазин"',
                    'supplierInvoiceNumber' => '1248373',
                ),
                // Assertions xpath
                'assertions' => array(
                    'number' => '10001',
                    'acceptanceDate' => '2013-03-18T12:56:00+0400',
                    'accepter' => 'Приемных Н.П.',
                    'legalEntity' => 'ООО "Магазин"',
                    'supplierInvoiceNumber' => '1248373'
                )
            )
        );
    }

    public function testGetInvoiceNotFound()
    {
        $store = $this->factory()->store()->getStore();
        $id = 'not_exists_id';

        $accessToken = $this->factory->oauth()->authAsDepartmentManager($store->id);

        $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $store->id . '/invoices/' . $id
        );

        $this->assertResponseCode(404);
    }

    public function testGetInvoiceNotFoundInAnotherStore()
    {
        $store1 = $this->factory->store()->getStore('41');
        $store2 = $this->factory->store()->getStore('43');
        $departmentManager = $this->factory->store()->getDepartmentManager($store1->id);
        $this->factory->store()->linkDepartmentManagers($departmentManager->id, $store2->id);

        $invoiceId = $this->createInvoice(array(), $store1->id);

        $accessToken = $this->factory->oauth()->auth($departmentManager);

        $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $store2->id . '/invoices/' . $invoiceId
        );

        $this->assertResponseCode(404);

        $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $store1->id . '/invoices/' . $invoiceId
        );

        $this->assertResponseCode(200);
    }

    /**
     * @dataProvider validateProvider
     */
    public function testPostInvoiceValidation($expectedCode, array $data, array $assertions = array())
    {
        $store = $this->factory()->store()->getStore();
        $supplier = $this->factory()->supplier()->getSupplier();

        $postData = $data + array(
            'supplier' => $supplier->id,
            'acceptanceDate' => '2013-03-18 12:56',
            'accepter' => 'Приемных Н.П.',
            'legalEntity' => 'ООО "Магазин"',
            'supplierInvoiceNumber' => '1248373',
        );

        $accessToken = $this->factory->oauth()->authAsDepartmentManager($store->id);

        $postResponse = $this->clientJsonRequest(
            $accessToken,
            'POST',
            '/api/1/stores/' . $store->id . '/invoices',
            $postData
        );

        $this->assertResponseCode($expectedCode);

        foreach ($assertions as $path => $expected) {
            Assert::assertJsonPathContains($expected, $path, $postResponse);
        }
    }

    /**
      * @dataProvider providerSupplierInvoiceDateIsValidAndAcceptanceDateIsInvalid
     */
    public function testPostInvoiceSupplierInvoiceDateIsValidAndAcceptanceDateIsInvalid(array $data)
    {
        $store = $this->factory()->store()->getStore();
        $invoiceData = $this->postInvoiceDataProvider();

        $postData = $data + $invoiceData['invoice']['data'];

        $accessToken = $this->factory->oauth()->authAsDepartmentManager($store->id);

        $postResponse = $this->clientJsonRequest(
            $accessToken,
            'POST',
            '/api/1/stores/' . $store->id . '/invoices',
            $postData
        );

        $this->assertResponseCode(400);

        Assert::assertJsonPathContains('Вы ввели неверную дату', 'children.acceptanceDate.errors.0', $postResponse);
        Assert::assertNotJsonHasPath('children.supplierInvoiceDate.errors.0', $postResponse);
    }

    /**
     * @return array
     */
    public function providerSupplierInvoiceDateIsValidAndAcceptanceDateIsInvalid()
    {
        return array(
            'supplierInvoiceDate in past' => array(
                array(
                    'acceptanceDate' => 'aaa',
                    'supplierInvoiceDate' => '2012-03-14'
                ),
            ),
            'supplierInvoiceDate in future' => array(
                array(
                    'acceptanceDate' => 'aaa',
                    'supplierInvoiceDate' => '2015-03-14'
                ),
            )
        );
    }

    /**
     * @dataProvider putInvoiceDataProvider
     */
    public function testPutInvoiceAction(
        array $postData,
        array $postAssertions,
        array $putData,
        $expectedCode,
        array $putAssertions
    ) {

        $store = $this->factory()->store()->getStore();
        $supplier1 = $this->factory()->supplier()->getSupplier($postData['supplier']);
        if (isset($putData['supplier'])) {
            $supplier2 = $this->factory()->supplier()->getSupplier($putData['supplier']);
            $putData['supplier'] = $supplier2->id;
        }

        $postData['supplier'] = $supplier1->id;

        $accessToken = $this->factory->oauth()->authAsDepartmentManager($store->id);

        $postJson = $this->clientJsonRequest(
            $accessToken,
            'POST',
            '/api/1/stores/' . $store->id . '/invoices',
            $postData
        );

        $this->assertResponseCode(201);
        Assert::assertJsonHasPath('id', $postJson);
        $invoiceId = $postJson['id'];

        foreach ($postAssertions as $jsonPath => $expected) {
            Assert::assertJsonPathContains($expected, $jsonPath, $postJson);
        }

        $putData += $postData;
        $putJson = $this->clientJsonRequest(
            $accessToken,
            'PUT',
            '/api/1/stores/' . $store->id . '/invoices/' . $invoiceId,
            $putData
        );

        $this->assertResponseCode($expectedCode);
        Assert::assertJsonPathEquals($invoiceId, 'id', $postJson);
        foreach ($putAssertions as $jsonPath => $expected) {
            Assert::assertJsonPathContains($expected, $jsonPath, $putJson);
        }
    }

    public function putInvoiceDataProvider()
    {
        $data = array(
            'supplier' => 'ООО "Поставщик"',
            'acceptanceDate' => '2013-03-18 12:56',
            'accepter' => 'Приемных Н.П.',
            'legalEntity' => 'ООО "Магазин"',
            'supplierInvoiceNumber' => '1248373',
        );
        $assertions = array(
            'number' => '10001',
            'supplier.name' => 'ООО "Поставщик"',
            'acceptanceDate' => '2013-03-18T12:56:00+0400',
            'accepter' => 'Приемных Н.П.',
            'legalEntity' => 'ООО "Магазин"',
            'supplierInvoiceNumber' => '1248373',
        );

        return array(
            'update supplier' => array(
                'postData' => $data,
                'postAssertions' => $assertions,
                'putData' => array(
                    'supplier' => 'ООО "Подставщик"',
                ),
                'expectedCode' => 200,
                'putAssertions' => array(
                    'supplier.name' => 'ООО "Подставщик"',
                ) + $assertions,
            )
        );
    }

    /**
     * @return array
     */
    public function validateProvider()
    {
        return array(
            /***********************************************************************************************
             * 'number'
             ***********************************************************************************************/
            'number is skipped' => array(
                201,
                array('number' => '10001'),
            ),
            'valid number 100 chars' => array(
                201,
                array('number' => str_repeat('z', 100)),
            ),
            /***********************************************************************************************
             * 'supplier'
             ***********************************************************************************************/
            'supplier is empty' => array(
                400,
                array('supplier' => ''),
                array('children.supplier.errors.0' => 'Выберите поставщика'),
            ),
            'supplier is invalid' => array(
                400,
                array('supplier' => 'aaaa'),
                array('children.supplier.errors.0' => 'Такого поставщика не существует'),
            ),
            /***********************************************************************************************
             * 'accepter'
             ***********************************************************************************************/
            'valid accepter' => array(
                201,
                array('accepter' => 'accepter'),
            ),
            'valid accepter 100 chars' => array(
                201,
                array('accepter' => str_repeat('z', 100)),
            ),
            'empty accepter' => array(
                400,
                array('accepter' => ''),
                array('children.accepter.errors.0' => 'Заполните это поле',),
            ),
            'not valid accepter too long' => array(
                400,
                array('accepter' => str_repeat("z", 105)),
                array('children.accepter.errors.0' => 'Не более 100 символов'),
            ),
            /***********************************************************************************************
             * 'legalEntity'
             ***********************************************************************************************/
            'valid legalEntity' => array(
                201,
                array('legalEntity' => 'legalEntity'),
            ),
            'valid legalEntity 300 chars' => array(
                201,
                array('legalEntity' => str_repeat('z', 300)),
            ),
            'empty legalEntity' => array(
                400,
                array('legalEntity' => ''),
                array('children.legalEntity.errors.0' => 'Заполните это поле'),
            ),
            'not valid legalEntity too long' => array(
                400,
                array('legalEntity' => str_repeat("z", 305)),
                array('children.legalEntity.errors.0' => 'Не более 300 символов'),
            ),
            /***********************************************************************************************
             * 'supplierInvoiceNumber'
             ***********************************************************************************************/
            'valid supplierInvoiceNumber' => array(
                201,
                array('supplierInvoiceNumber' => 'supplierInvoiceNumber'),
            ),
            'valid supplierInvoiceNumber 100 chars' => array(
                201,
                array('supplierInvoiceNumber' => str_repeat('z', 100)),
            ),
            'empty supplierInvoiceNumber' => array(
                201,
                array('supplierInvoiceNumber' => ''),
            ),
            'not valid supplierInvoiceNumber too long' => array(
                400,
                array('supplierInvoiceNumber' => str_repeat("z", 105)),
                array('children.supplierInvoiceNumber.errors.0' => 'Не более 100 символов'),
            ),
            /***********************************************************************************************
             * 'acceptanceDate'
             ***********************************************************************************************/
            'valid acceptanceDate 2013-03-26T12:34:56' => array(
                201,
                array('acceptanceDate' => '2013-03-26T12:34:56'),
                array("acceptanceDate" => '2013-03-26T12:34:56+0400')
            ),
            'valid acceptanceDate 2013-03-26' => array(
                201,
                array('acceptanceDate' => '2013-03-26'),
                array("acceptanceDate" => '2013-03-26T00:00:00+0400')
            ),
            'valid acceptanceDate 2013-03-26 12:34' => array(
                201,
                array('acceptanceDate' => '2013-03-26 12:34'),
                array("acceptanceDate" => '2013-03-26T12:34:00+0400')
            ),
            'valid acceptanceDate 2013-03-26 12:34:45' => array(
                201,
                array('acceptanceDate' => '2013-03-26 12:34:45'),
                array("acceptanceDate" => '2013-03-26T12:34:45+0400')
            ),
            'empty acceptanceDate' => array(
                400,
                array('acceptanceDate' => ''),
                array('children.acceptanceDate.errors.0' => 'Заполните это поле'),
            ),
            'not valid acceptanceDate 2013-02-31' => array(
                400,
                array('acceptanceDate' => '2013-02-31'),
                array('children.acceptanceDate.errors.0' => 'Вы ввели неверную дату'),
            ),
            'not valid acceptanceDate aaa' => array(
                400,
                array('acceptanceDate' => 'aaa'),
                array(
                    'children.acceptanceDate.errors.0'
                    =>
                    'Вы ввели неверную дату',
                ),
            ),
            'not valid acceptanceDate __.__.____ __:__' => array(
                400,
                array('acceptanceDate' => '__.__.____ __:__'),
                array(
                    'children.acceptanceDate.errors.0'
                    =>
                        'Вы ввели неверную дату',
                ),
            ),
            /***********************************************************************************************
             * 'supplierInvoiceDate'
             ***********************************************************************************************/
            'supplierInvoiceDate should not be present' => array(
                400,
                array('supplierInvoiceDate' => '2013-02-31'),
                array(
                    'errors.0'
                    =>
                    'Эта форма не должна содержать дополнительных полей: "supplierInvoiceDate"',
                ),
            ),
            /***********************************************************************************************
             * 'createdDate'
             ***********************************************************************************************/
            'not valid createdDate' => array(
                400,
                array('createdDate' => '2013-03-26T12:34:56'),
                array(
                    'errors.0'
                    =>
                    'Эта форма не должна содержать дополнительных полей',
                ),
            ),
        );
    }

    public function testDepartmentManagerCantGetInvoiceFromAnotherStore()
    {
        $store1 = $this->factory()->store()->getStore('41');
        $store2 = $this->factory()->store()->getStore('43');

        $accessToken1 = $this->factory()->oauth()->authAsDepartmentManager($store1->id);
        $accessToken2 = $this->factory()->oauth()->authAsDepartmentManager($store2->id);

        $invoiceId1 = $this->createInvoice(array(), $store1->id);
        $invoiceId2 = $this->createInvoice(array(), $store2->id);

        $this->clientJsonRequest(
            $accessToken2,
            'GET',
            '/api/1/stores/' . $store1->id . '/invoices/' . $invoiceId1
        );

        $this->assertResponseCode(403);

        $this->clientJsonRequest(
            $accessToken1,
            'GET',
            '/api/1/stores/' . $store2->id . '/invoices/' . $invoiceId2
        );

        $this->assertResponseCode(403);

        $this->clientJsonRequest(
            $accessToken1,
            'GET',
            '/api/1/stores/' . $store1->id . '/invoices/' . $invoiceId1
        );

        $this->assertResponseCode(200);

        $this->clientJsonRequest(
            $accessToken2,
            'GET',
            '/api/1/stores/' . $store2->id . '/invoices/' . $invoiceId2
        );

        $this->assertResponseCode(200);
    }

    /**
     * @param string $query
     * @param int $count
     * @param array $assertions
     *
     * @dataProvider invoiceFilterProvider
     */
    public function testInvoicesFilter($query, $count, array $assertions = array())
    {
        $store = $this->factory()->store()->getStore();
        $productId1 = $this->createProduct('111');
        $productId2 = $this->createProduct('222');
        $productId3 = $this->createProduct('333');

        $invoiceData1 = array(
            'supplierInvoiceNumber' => 'ФРГ-1945'
        );

        $invoiceId1 = $this->createInvoice($invoiceData1, $store->id);
        $this->createInvoiceProduct($invoiceId1, $productId1, 10, 6.98, $store->id);

        $invoiceData2 = array(
            'supplierInvoiceNumber' => '10001'
        );

        $invoiceId2 = $this->createInvoice($invoiceData2, $store->id);
        $this->createInvoiceProduct($invoiceId2, $productId2, 5, 10.12, $store->id);

        $invoiceData3 = array(
            'supplierInvoiceNumber' => '10003'
        );

        $invoiceId3 = $this->createInvoice($invoiceData3, $store->id);
        $this->createInvoiceProduct($invoiceId3, $productId3, 7, 67.32, $store->id);

        $accessToken = $this->factory->oauth()->authAsDepartmentManager($store->id);
        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $store->id . '/invoices',
            null,
            array('numberOrSupplierInvoiceNumber' => $query)
        );

        $this->assertResponseCode(200);
        Assert::assertJsonPathCount($count, '*.id', $response);
        $this->performJsonAssertions($response, $assertions);
    }

    /**
     * @return array
     */
    public function invoiceFilterProvider()
    {
        return array(
            'one by number' => array(
                '10002',
                1,
                array(
                    '0.number' => '10002',
                    '0._meta.highlights.number' => true,
                )
            ),
            'one by supplierInvoiceNumber' => array(
                'ФРГ-1945',
                1,
                array(
                    '0.supplierInvoiceNumber' => 'ФРГ-1945',
                    '0._meta.highlights.supplierInvoiceNumber' => true,
                )
            ),
            'one by both number and supplierInvoiceNumber' => array(
                '10003',
                1,
                array(
                    '0.supplierInvoiceNumber' => '10003',
                    '0.number' => '10003',
                    '0._meta.highlights.number' => true,
                    '0._meta.highlights.supplierInvoiceNumber' => true,
                )
            ),
            'none found: not existing number' => array(
                '1234',
                0,
            ),
            'none found: empty number' => array(
                '',
                0,
            ),
            'none found: partial number' => array(
                '1000',
                0,
            ),
            'two: one by number and one by supplierInvoiceNumber' => array(
                '10001',
                2,
                array(
                    '0.number' => '10001',
                    '1.supplierInvoiceNumber' => '10001',
                    '0._meta.highlights.number' => true,
                    '1._meta.highlights.supplierInvoiceNumber' => true,
                )
            ),
            'one by number check invoice products' => array(
                '10002',
                1,
                array(
                    '0.number' => '10002',
                    '0._meta.highlights.number' => true,
                )
            ),
        );
    }

    public function testInvoicesFilterOrder()
    {
        $store = $this->factory()->store()->getStore();
        $productId1 = $this->createProduct('111');
        $productId2 = $this->createProduct('222');

        $invoiceData1 = array(
            'supplierInvoiceNumber' => 'ФРГ-1945',
            'acceptanceDate' => '2013-03-17T16:12:33+0400',
        );

        $invoiceId1 = $this->createInvoice($invoiceData1, $store->id);
        $this->createInvoiceProduct($invoiceId1, $productId1, 10, 6.98);

        $invoiceData2 = array(
            'supplierInvoiceNumber' => '10001',
            'acceptanceDate' => '2013-03-16T14:54:23+0400'
        );

        $invoiceId2 = $this->createInvoice($invoiceData2, $store->id);
        $this->createInvoiceProduct($invoiceId2, $productId2, 5, 10.12);

        $accessToken = $this->factory->oauth()->authAsDepartmentManager($store->id);
        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $store->id . '/invoices',
            null,
            array('numberOrSupplierInvoiceNumber' => '10001')
        );

        $this->assertResponseCode(200);
        Assert::assertJsonPathCount(2, '*.id', $response);
        Assert::assertJsonPathEquals('10001', '0.number', $response);
        Assert::assertJsonPathEquals('10002', '1.number', $response);

        $invoiceData3 = array(
            'supplierInvoiceNumber' => 'ФРГ-1945',
            'acceptanceDate' => '2013-03-15T16:12:33+0400'
        );

        $invoiceId3 = $this->createInvoice($invoiceData3, $store->id);
        $this->createInvoiceProduct($invoiceId3, $productId1, 10, 6.98, $store->id);

        $invoiceData4 = array(
            'supplierInvoiceNumber' => '10003',
            'acceptanceDate' => '2013-03-16T14:54:23+0400'
        );

        $invoiceId4 = $this->createInvoice($invoiceData4, $store->id);
        $this->createInvoiceProduct($invoiceId4, $productId2, 5, 10.12, $store->id);

        $accessToken = $this->factory->oauth()->authAsDepartmentManager($store->id);
        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $store->id . '/invoices',
            null,
            array('numberOrSupplierInvoiceNumber' => '10003')
        );

        $this->assertResponseCode(200);
        Assert::assertJsonPathCount(2, '*.id', $response);
        Assert::assertJsonPathEquals('10004', '0.number', $response);
        Assert::assertJsonPathEquals('10003', '1.number', $response);
    }

    public function testInvoiceWithVATFields()
    {
        $store = $this->factory()->store()->getStore();
        $productId1 = $this->createProduct(array('sku' => '111', 'vat' => '10'));
        $productId2 = $this->createProduct(array('sku' => '222', 'vat' => '18'));

        $invoiceData1 = array(
            'supplierInvoiceNumber' => 'ФРГ-1945',
            'acceptanceDate' => '2013-03-17T16:12:33+0400',
            'includesVAT' => true,
        );

        $invoiceId1 = $this->createInvoice($invoiceData1, $store->id);
        $invoiceProductId1 = $this->
            createInvoiceProduct($invoiceId1, $productId1, 99.99, 36.78);
        $invoiceProductId2 = $this->
            createInvoiceProduct($invoiceId1, $productId2, 10.77, 6.98);

        $accessToken = $this->factory->oauth()->authAsDepartmentManager($store->id);
        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $store->id . '/invoices/' . $invoiceId1
        );

        $this->assertResponseCode(200);
        Assert::assertJsonPathEquals(true, 'includesVAT', $response);
        Assert::assertJsonPathEquals(3752.8, 'sumTotal', $response);
        Assert::assertJsonPathEquals(3407.42, 'sumTotalWithoutVAT', $response);
        Assert::assertJsonPathEquals(345.39, 'totalAmountVAT', $response);


        $response = $this->clientJsonRequest(
            $accessToken,
            'DELETE',
            '/api/1/stores/' . $store->id . '/invoices/' . $invoiceId1 . '/products/' . $invoiceProductId2
        );
        $this->assertResponseCode(204);
        $this->assertNull($response);

        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $store->id . '/invoices/' . $invoiceId1
        );

        $this->assertResponseCode(200);
        Assert::assertJsonPathEquals(true, 'includesVAT', $response);
        Assert::assertJsonPathEquals(3677.63, 'sumTotal', $response);
        Assert::assertJsonPathEquals(3343.66, 'sumTotalWithoutVAT', $response);
        Assert::assertJsonPathEquals(333.97, 'totalAmountVAT', $response);


        $response = $this->clientJsonRequest(
            $accessToken,
            'DELETE',
            '/api/1/stores/' . $store->id . '/invoices/' . $invoiceId1 . '/products/' . $invoiceProductId1
        );
        $this->assertResponseCode(204);
        $this->assertNull($response);

        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $store->id . '/invoices/' . $invoiceId1
        );

        $this->assertResponseCode(200);
        Assert::assertJsonPathEquals(true, 'includesVAT', $response);
        Assert::assertJsonPathEquals(0, 'sumTotal', $response);
        Assert::assertJsonPathEquals(0, 'sumTotalWithoutVAT', $response);
        Assert::assertJsonPathEquals(0, 'totalAmountVAT', $response);
    }

    /**
     * Проверяем что указав цену без НДС получим данные соответствующие данным теста выше
     */
    public function testInvoiceWithVATFieldsWithoutVAT()
    {
        $store = $this->factory()->store()->getStore();
        $productId1 = $this->createProduct(array('sku' => '111', 'vat' => '10'));
        $productId2 = $this->createProduct(array('sku' => '222', 'vat' => '18'));

        $invoiceData1 = array(
            'supplierInvoiceNumber' => 'ФРГ-1945',
            'acceptanceDate' => '2013-03-17T16:12:33+0400',
            'includesVAT' => false,
        );

        $invoiceId1 = $this->createInvoice($invoiceData1, $store->id);
        $invoiceProductId1 = $this->createInvoiceProduct($invoiceId1, $productId1, 99.99, 33.44);
        $invoiceProductId2 = $this->createInvoiceProduct($invoiceId1, $productId2, 10.77, 5.92);

        $accessToken = $this->factory->oauth()->authAsDepartmentManager($store->id);
        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $store->id . '/invoices/' . $invoiceId1
        );

        $this->assertResponseCode(200);
        Assert::assertJsonPathEquals(false, 'includesVAT', $response);
        Assert::assertJsonPathEquals(3752.8, 'sumTotal', $response);
        Assert::assertJsonPathEquals(3407.42, 'sumTotalWithoutVAT', $response);
        Assert::assertJsonPathEquals(345.39, 'totalAmountVAT', $response);


        $response = $this->clientJsonRequest(
            $accessToken,
            'DELETE',
            '/api/1/stores/' . $store->id . '/invoices/' . $invoiceId1 . '/products/' . $invoiceProductId2
        );
        $this->assertResponseCode(204);
        $this->assertNull($response);

        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $store->id . '/invoices/' . $invoiceId1
        );

        $this->assertResponseCode(200);
        Assert::assertJsonPathEquals(false, 'includesVAT', $response);
        Assert::assertJsonPathEquals(3677.63, 'sumTotal', $response);
        Assert::assertJsonPathEquals(3343.66, 'sumTotalWithoutVAT', $response);
        Assert::assertJsonPathEquals(333.97, 'totalAmountVAT', $response);


        $response = $this->clientJsonRequest(
            $accessToken,
            'DELETE',
            '/api/1/stores/' . $store->id . '/invoices/' . $invoiceId1 . '/products/' . $invoiceProductId1
        );
        $this->assertResponseCode(204);
        $this->assertNull($response);

        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $store->id . '/invoices/' . $invoiceId1
        );

        $this->assertResponseCode(200);
        Assert::assertJsonPathEquals(false, 'includesVAT', $response);
        Assert::assertJsonPathEquals(0, 'sumTotal', $response);
        Assert::assertJsonPathEquals(0, 'sumTotalWithoutVAT', $response);
        Assert::assertJsonPathEquals(0, 'totalAmountVAT', $response);
    }

    public function testInvoiceChangeIncludesVAT()
    {
        $store = $this->factory()->store()->getStore();
        $productId1 = $this->createProduct(array('sku' => '111', 'vat' => '10'));
        $this->createProduct(array('sku' => '222', 'vat' => '18'));
        $supplier = $this->factory()->supplier()->getSupplier();

        $invoiceData1 = array(
            'supplier' => $supplier->id,
            'acceptanceDate' => '2013-03-18 12:56',
            'accepter' => 'Приемных Н.П.',
            'legalEntity' => 'ООО "Магазин"',
            'supplierInvoiceNumber' => '1248373',
            'includesVAT' => true,
            'products' => array(
                array(
                    'product' => $productId1,
                    'quantity' => '99.99',
                    'priceEntered' => '36.78'
                )
            )
        );

        $accessToken = $this->factory->oauth()->authAsDepartmentManager($store->id);
        $response = $this->clientJsonRequest(
            $accessToken,
            'POST',
            '/api/1/stores/' . $store->id . '/invoices',
            $invoiceData1
        );

        $this->assertResponseCode(201);

        $invoiceId1 = $response['id'];

        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $store->id . '/invoices/' . $invoiceId1
        );

        $this->assertResponseCode(200);
        Assert::assertJsonPathEquals(true, 'includesVAT', $response);
        Assert::assertJsonPathEquals(3677.63, 'sumTotal', $response);
        Assert::assertJsonPathEquals(3343.66, 'sumTotalWithoutVAT', $response);
        Assert::assertJsonPathEquals(333.97, 'totalAmountVAT', $response);

        $invoiceData1['includesVAT'] = false;

        $response = $this->clientJsonRequest(
            $accessToken,
            'PUT',
            '/api/1/stores/' . $store->id . '/invoices/' . $invoiceId1,
            $invoiceData1
        );
        $this->assertResponseCode(200);
        Assert::assertJsonPathEquals(false, 'includesVAT', $response);
        Assert::assertJsonPathEquals(4045.60, 'sumTotal', $response);
        Assert::assertJsonPathEquals(3677.63, 'sumTotalWithoutVAT', $response);
        Assert::assertJsonPathEquals(367.96, 'totalAmountVAT', $response);

        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $store->id . '/invoices/' . $invoiceId1
        );

        $this->assertResponseCode(200);
        Assert::assertJsonPathEquals(false, 'includesVAT', $response);
        Assert::assertJsonPathEquals(4045.60, 'sumTotal', $response);
        Assert::assertJsonPathEquals(3677.63, 'sumTotalWithoutVAT', $response);
        Assert::assertJsonPathEquals(367.96, 'totalAmountVAT', $response);
    }

    public function testProductSubCategoryIsNotExposed()
    {
        $storeId = $this->factory->store()->getStoreId();

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
            '/api/1/stores/' . $storeId .'/invoices/' . $invoiceId
        );

        $this->assertResponseCode(200);
        Assert::assertNotJsonHasPath('products.*.product.subCategory.category', $getResponse);
    }
}
