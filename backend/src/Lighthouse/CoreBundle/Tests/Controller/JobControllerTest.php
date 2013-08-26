<?php

namespace Lighthouse\CoreBundle\Tests\Controller;

use Doctrine\ODM\MongoDB\DocumentManager;
use Lighthouse\CoreBundle\Document\Job\JobManager;
use Lighthouse\CoreBundle\Document\User\User;
use Lighthouse\CoreBundle\Test\Assert;
use Lighthouse\CoreBundle\Test\WebTestCase;

class JobControllerTest extends WebTestCase
{
    protected function setUp()
    {
        parent::setUp();

        /* @var JobManager $jobManager */
        $jobManager = $this->getContainer()->get('lighthouse.core.job.manager');
        $jobManager->startWatchingTubes()->purgeTubes()->stopWatchingTubes();
    }

    public function testRecalcProductProductPrice()
    {
        $this->clearMongoDb();

        $commercialAccessToken = $this->authAsRole(User::ROLE_COMMERCIAL_MANAGER);

        $getResponse = $this->clientJsonRequest(
            $commercialAccessToken,
            'GET',
            '/api/1/jobs'
        );

        $this->assertResponseCode(200);

        Assert::assertJsonPathCount(0, '*.id', $getResponse);

        $storeId1 = $this->createStore('1');
        $storeId2 = $this->createStore('2');
        $storeId3 = $this->createStore('3');

        $productData = array(
            'sku' => 'Печенье Юбилейное',
            'purchasePrice' => 20,
            'retailMarkupMin' => 10,
            'retailMarkupMax' => 30,
            'retailPricePreference' => 'retailMarkup',
        );

        $productId = $this->createProduct($productData);

        $storeProductData1 = array(
            'retailPrice' => 22,
            'retailPricePreference' => 'retailPrice',
        );

        $this->updateStoreProduct($storeId1, $productId, $storeProductData1);

        $storeProductData2 = array(
            'retailPrice' => 26,
            'retailPricePreference' => 'retailPrice',
        );

        $this->updateStoreProduct($storeId2, $productId, $storeProductData2);

        $storeProductData3 = array(
            'retailPrice' => 23,
            'retailPricePreference' => 'retailPrice',
        );

        $this->updateStoreProduct($storeId3, $productId, $storeProductData3);

        $updateProductData = array(
            'retailPriceMin' => 23,
            'retailPriceMax' => 24,
            'retailPricePreference' => 'retailPrice',
        ) + $productData;

        $this->updateProduct($productId, $updateProductData);

        $getResponse = $this->clientJsonRequest(
            $commercialAccessToken,
            'GET',
            '/api/1/jobs'
        );

        $this->assertResponseCode(200);

        Assert::assertJsonPathCount(1, '*.id', $getResponse);
        Assert::assertJsonPathEquals('recalc_product_price', '*.type', $getResponse);
        Assert::assertJsonPathEquals('pending', '*.status', $getResponse);

        /* @var DocumentManager $dm */
        $dm = $this->getContainer()->get('doctrine.odm.mongodb.document_manager');
        $dm->clear();

        /* @var JobManager $jobManager */
        $jobManager = $this->getContainer()->get('lighthouse.core.job.manager');

        $jobManager->startWatchingTubes();
        $job = $jobManager->reserveJob(0);
        $jobManager->stopWatchingTubes();

        $getResponse = $this->clientJsonRequest(
            $commercialAccessToken,
            'GET',
            '/api/1/jobs'
        );

        $this->assertResponseCode(200);

        Assert::assertJsonPathCount(1, '*.id', $getResponse);
        Assert::assertJsonPathEquals('recalc_product_price', '*.type', $getResponse);
        Assert::assertJsonPathEquals('processing', '*.status', $getResponse);

        $jobManager->startWatchingTubes();
        $jobManager->processJob($job);
        $jobManager->stopWatchingTubes();

        $getResponse = $this->clientJsonRequest(
            $commercialAccessToken,
            'GET',
            '/api/1/jobs'
        );

        $this->assertResponseCode(200);

        Assert::assertJsonPathCount(1, '*.id', $getResponse);
        Assert::assertJsonPathEquals('recalc_product_price', '*.type', $getResponse);
        Assert::assertJsonPathEquals('success', '*.status', $getResponse);

        $this->assertStoreProduct(
            $storeId1,
            $productId,
            array(
                'retailPrice' => '23.00',
            )
        );

        $this->assertStoreProduct(
            $storeId2,
            $productId,
            array(
                'retailPrice' => '24.00',
            )
        );

        $this->assertStoreProduct(
            $storeId3,
            $productId,
            array(
                'retailPrice' => '23.00',
            )
        );
    }

    /**
     * @param $storeId
     * @param $productId
     * @param array $assertions
     */
    protected function assertStoreProduct($storeId, $productId, array $assertions)
    {
        $storeManager = $this->getStoreManager($storeId);

        $accessToken = $this->auth($storeManager, 'password');

        $getResponse = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $storeId . '/products/' . $productId
        );

        $this->assertResponseCode(200);
        $this->performJsonAssertions($getResponse, $assertions);
    }
}
