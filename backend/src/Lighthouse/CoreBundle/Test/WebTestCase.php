<?php

namespace Lighthouse\CoreBundle\Test;

use DateTime;
use Lighthouse\CoreBundle\Document\Product\Type\UnitType;
use Lighthouse\CoreBundle\Document\User\User;
use Lighthouse\CoreBundle\Test\Client\JsonRequest;
use Lighthouse\CoreBundle\Test\Client\Client;
use PHPUnit_Framework_ExpectationFailedException;

/**
 * @codeCoverageIgnore
 */
class WebTestCase extends ContainerAwareTestCase
{
    /**
     * @var Client
     */
    protected $client;

    protected function setUp()
    {
        $this->client = static::createClient();
        $this->clearMongoDb();
        $this->clearJobs();
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->client = null;
    }

    /**
     * @param \stdClass|string $token
     * @param string $method
     * @param string $uri
     * @param array $data
     * @param array $parameters
     * @param array $server
     * @param bool $changeHistory
     * @return array
     * @throws \Exception
     */
    protected function clientJsonRequest(
        $token,
        $method,
        $uri,
        $data = null,
        array $parameters = array(),
        array $server = array(),
        $changeHistory = false
    ) {
        $request = new JsonRequest($uri, $method);

        $request->parameters = $parameters;
        $request->server = $server;
        $request->changeHistory = $changeHistory;

        if ($token) {
            $request->setAccessToken($token);
        }

        $request->setJsonData($data);
        $request->setJsonHeaders();

        $this->client->shutdownKernelBeforeRequest();

        return $this->client->jsonRequest($request);
    }

    /**
     * @deprecated
     * @param string $productId
     * @param array $data
     */
    protected function updateProduct($productId, array $data)
    {
        $subCategory = $this->factory()->catalog()->getSubCategory();

        $productData = $data + array(
            'name' => 'Кефир "Веселый Молочник" 1% 950гр',
            'type' => UnitType::TYPE,
            'barcode' => uniqid('', true),
            'purchasePrice' => 3048,
            'vat' => 10,
            'vendor' => 'Вимм-Билль-Данн',
            'vendorCountry' => 'Россия',
            'info' => 'Классный кефирчик, употребляю давно, всем рекомендую для поднятия тонуса',
            'subCategory' => $subCategory->id,
        );

        $accessToken = $this->factory()->oauth()->authAsRole(User::ROLE_COMMERCIAL_MANAGER);

        $postResponse = $this->clientJsonRequest(
            $accessToken,
            'PUT',
            "/api/1/products/{$productId}",
            $productData
        );

        $this->assertResponseCode(200);
        Assert::assertJsonHasPath('id', $postResponse);

        return $postResponse['id'];
    }

    /**
     * @deprecated
     * @param string $productId
     * @param array $barcodes
     */
    protected function updateProductBarcodes($productId, array $barcodes)
    {
        $accessToken = $this->factory()->oauth()->authAsRole(User::ROLE_COMMERCIAL_MANAGER);
        $this->clientJsonRequest(
            $accessToken,
            'PUT',
            '/api/1/products/' . $productId . '/barcodes',
            array(
                'barcodes' => $barcodes,
            )
        );
        $this->assertResponseCode(200);
    }

    /**
     * @param mixed $json
     * @param array $assertions
     * @param bool  $contains
     */
    protected function performJsonAssertions($json, array $assertions, $contains = false)
    {
        foreach ($assertions as $path => $expected) {
            if (null === $expected) {
                Assert::assertNotJsonHasPath($path, $json);
            } elseif ($contains) {
                Assert::assertJsonPathContains($expected, $path, $json);
            } else {
                Assert::assertJsonPathEquals($expected, $path, $json);
            }
        }
    }

    /**
     * @param string $productId
     * @param array $assertions
     */
    protected function assertProduct($productId, array $assertions)
    {
        $accessToken = $this->factory()->oauth()->authAsRole(User::ROLE_COMMERCIAL_MANAGER);

        $request = new JsonRequest('/api/1/products/' . $productId);
        $request->setAccessToken($accessToken);

        $productJson = $this->client->jsonRequest($request);

        $this->assertResponseCode(200);

        $this->performJsonAssertions($productJson, $assertions);
    }

    /**
     * @param string $storeId
     * @param string $productId
     * @param array  $assertions
     * @param string $message
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    protected function assertStoreProduct($storeId, $productId, array $assertions, $message = '')
    {
        $accessToken = $this->factory()->oauth()->authAsStoreManager($storeId);

        $storeProductJson = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $storeId . '/products/' . $productId
        );

        $this->assertResponseCode(200);

        try {
            $this->performJsonAssertions($storeProductJson, $assertions);
        } catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $message.= ($message) ? '. ' . $e->getMessage() : $e->getMessage();
            throw new PHPUnit_Framework_ExpectationFailedException(
                $message,
                $e->getComparisonFailure(),
                $e->getPrevious()
            );
        }
    }

    /**
     * @param string $storeId
     * @param string $productId
     * @param int $inventory
     * @param float $lastPurchasePrice
     */
    protected function assertStoreProductTotals($storeId, $productId, $inventory, $lastPurchasePrice = null)
    {
        $assertions = array(
            'inventory' => $inventory,
            'lastPurchasePrice' => $lastPurchasePrice
        );

        $this->assertStoreProduct($storeId, $productId, $assertions);
    }

    /**
     * @deprecated
     * @param string $storeId
     * @param string $productId
     * @param array $productData
     */
    public function updateStoreProduct(
        $storeId,
        $productId,
        array $productData = array()
    ) {
        $accessToken = $this->factory()->oauth()->authAsStoreManager($storeId);

        $this->clientJsonRequest(
            $accessToken,
            'PUT',
            '/api/1/stores/' . $storeId . '/products/' . $productId,
            $productData
        );

        $this->assertResponseCode(200);
    }

    /**
     * @param integer $expectedCode
     * @param string $message
     */
    public function assertResponseCode($expectedCode, $message = '')
    {
        Assert::assertResponseCode($expectedCode, $this->client, $message);
    }

    /**
     * @param string $name
     * @param string $value
     * @return mixed
     */
    public function createConfig($name = 'test-config', $value = 'test-config-value')
    {
        $configData = array(
            'name' => $name,
            'value' => $value,
        );

        $accessToken = $this->factory()->oauth()->authAsRole('ROLE_ADMINISTRATOR');

        $postResponse = $this->clientJsonRequest(
            $accessToken,
            'POST',
            '/api/1/configs',
            $configData
        );

        $this->assertResponseCode(201);

        Assert::assertJsonPathEquals($name, 'name', $postResponse);
        Assert::assertJsonPathEquals($value, 'value', $postResponse);
        Assert::assertJsonHasPath('id', $postResponse);

        return $postResponse['id'];
    }

    /**
     * @param string $configId
     * @param string $name
     * @param string $value
     * @return string
     */
    public function updateConfig($configId, $name = 'test-config', $value = 'test-config-value')
    {
        $configData = array(
            'name' => $name,
            'value' => $value,
        );

        $accessToken = $this->factory()->oauth()->authAsRole('ROLE_ADMINISTRATOR');

        $postResponse = $this->clientJsonRequest(
            $accessToken,
            "PUT",
            "/api/1/configs/" . $configId,
            $configData
        );

        $this->assertResponseCode(200);

        Assert::assertJsonPathEquals($name, 'name', $postResponse);
        if ($value !== '') {
            Assert::assertJsonPathEquals($value, 'value', $postResponse);
        }
        Assert::assertJsonPathEquals($configId, 'id', $postResponse);

        return $postResponse['id'];
    }

    /**
     * @param $accessToken
     * @param string $storeId
     * @param string $orderId
     * @param string $supplierId
     * @param array $orderProducts
     */
    protected function assertOrder($accessToken, $storeId, $orderId, $supplierId, array $orderProducts)
    {
        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $storeId . '/orders/' . $orderId
        );

        $this->assertResponseCode(200);

        Assert::assertJsonPathEquals($supplierId, 'supplier.id', $response);
        Assert::assertJsonPathEquals(10001, 'number', $response);
        Assert::assertJsonPathCount(count($orderProducts), 'products.*.id', $response);
        foreach ($orderProducts as $orderProduct) {
            Assert::assertJsonPathEquals($orderProduct['quantity'], 'products.*.quantity', $response);
            Assert::assertJsonPathEquals($orderProduct['product'], 'products.*.product.product.id', $response);
        }
    }

    /**
     * @param string $modify
     * @param string $format
     * @return string
     */
    protected function createDate($modify, $format = DateTime::ISO8601)
    {
        $date = new DateTime('now');
        $date->modify($modify);
        return $date->format($format);
    }
}
