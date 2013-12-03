<?php

namespace Lighthouse\CoreBundle\Tests\Controller;

use Lighthouse\CoreBundle\Document\User\User;
use Lighthouse\CoreBundle\Service\StoreGrossSalesReportService;
use Lighthouse\CoreBundle\Test\WebTestCase;
use DateTime;

class ReportControllerTest extends WebTestCase
{
    /**
     * @return StoreGrossSalesReportService
     */
    public function getGrossSalesReportService()
    {
        return $this->getContainer()->get('lighthouse.core.service.store.report.gross_sales');
    }

    public function testGetStoreGrossSalesReportsByTime()
    {
        $storeId = $this->factory->getStore();
        $accessToken = $this->factory->authAsStoreManager($storeId);

        $product1Id = $this->createProduct('1');
        $product2Id = $this->createProduct('2');
        $product3Id = $this->createProduct('3');

        $sales = array(
            array(
                'storeId' => $storeId,
                'createdDate' => "8:01",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),
            array(
                'storeId' => $storeId,
                'createdDate' => "9:01",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),
            array(
                'storeId' => $storeId,
                'createdDate' => "10:01",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),
            array(
                'storeId' => $storeId,
                'createdDate' => "11:01",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),

            array(
                'storeId' => $storeId,
                'createdDate' => '-1 days 8:01',
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),
            array(
                'storeId' => $storeId,
                'createdDate' => "-1 days 9:01",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),
            array(
                'storeId' => $storeId,
                'createdDate' => "-1 days 10:01",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),

            array(
                'storeId' => $storeId,
                'createdDate' => "-7 days 8:01",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),
            array(
                'storeId' => $storeId,
                'createdDate' => "-7 days 9:01",
                'sumTotal' => 705.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.00
                    ),
                ),
            ),
            array(
                'storeId' => $storeId,
                'createdDate' => "-7 days 10:01",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),
        );

        $this->factory->createSales($sales);

        $storeGrossSalesReportService = $this->getGrossSalesReportService();

        $storeGrossSalesReportService->recalculateStoreGrossSalesReport();

        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $storeId . '/reports/grossSales',
            null,
            array('time' => date('c', strtotime("10:35:47")))
        );

        $this->assertResponseCode(200);

        $expected = array(
            'today' => array(
                'now' => array(
                    'date' => date(DateTime::ISO8601, strtotime("10:00")),
                    'value' => "1207.06",
                )
            ),
            'yesterday' => array(
                'now' => array(
                    'date' => date(DateTime::ISO8601, strtotime("-1 day 10:00")),
                    'value' => "1207.06",
                    'diff' => "0.00",
                ),
                'dayEnd' => array(
                    'date' => date(DateTime::ISO8601, strtotime("-1 day 23:59:59")),
                    'value' => "1810.59",
                ),
            ),
            'weekAgo' => array(
                'now' => array(
                    'date' => date(DateTime::ISO8601, strtotime("-7 day 10:00")),
                    'value' => "1309.06",
                    'diff' => "-7.79"
                ),
                'dayEnd' => array(
                    'date' => date(DateTime::ISO8601, strtotime("-7 day 23:59:59")),
                    'value' => "1912.59",
                ),
            ),
        );

        $this->assertEquals($expected, $response);
    }

    public function testGetStoreGrossSalesReportsByTimeEmptyReports()
    {
        $storeId = $this->factory->getStore();
        $accessToken = $this->factory->authAsStoreManager($storeId);

        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $storeId . '/reports/grossSales',
            null,
            array('time' => date('c', strtotime("10:35:47")))
        );

        $this->assertResponseCode(200);

        $expected = array(
            'today' => array(
                'now' => array(
                    'date' => date(DateTime::ISO8601, strtotime("10:00")),
                    'value' => "0.00",
                )
            ),
            'yesterday' => array(
                'now' => array(
                    'date' => date(DateTime::ISO8601, strtotime("-1 day 10:00")),
                    'value' => "0.00",
                ),
                'dayEnd' => array(
                    'date' => date(DateTime::ISO8601, strtotime("-1 day 23:59:59")),
                    'value' => "0.00",
                ),
            ),
            'weekAgo' => array(
                'now' => array(
                    'date' => date(DateTime::ISO8601, strtotime("-7 day 10:00")),
                    'value' => "0.00",
                ),
                'dayEnd' => array(
                    'date' => date(DateTime::ISO8601, strtotime("-7 day 23:59:59")),
                    'value' => "0.00",
                ),
            ),
        );

        $this->assertEquals($expected, $response);
    }

    public function testAccessGetStoreGrossSalesReport()
    {
        $storeId = $this->factory->getStore();
        $storeManagerToken = $this->factory->authAsStoreManager($storeId);
        $departmentManagerToken = $this->factory->authAsDepartmentManager($storeId);
        $storeManagerOtherStoreToken = $this->factory->authAsRole(User::ROLE_STORE_MANAGER);
        $commercialManagerToken = $this->factory->authAsRole(User::ROLE_COMMERCIAL_MANAGER);
        $departmentManagerOtherStoreToken = $this->factory->authAsRole(User::ROLE_DEPARTMENT_MANAGER);
        $administratorToken = $this->factory->authAsRole(User::ROLE_ADMINISTRATOR);


        $response = $this->clientJsonRequest(
            $storeManagerToken,
            'GET',
            '/api/1/stores/' . $storeId . '/reports/grossSales'
        );
        $this->assertResponseCode(200);

        $response = $this->clientJsonRequest(
            $departmentManagerToken,
            'GET',
            '/api/1/stores/' . $storeId . '/reports/grossSales'
        );
        $this->assertResponseCode(403);

        $response = $this->clientJsonRequest(
            $storeManagerOtherStoreToken,
            'GET',
            '/api/1/stores/' . $storeId . '/reports/grossSales'
        );
        $this->assertResponseCode(403);

        $response = $this->clientJsonRequest(
            $commercialManagerToken,
            'GET',
            '/api/1/stores/' . $storeId . '/reports/grossSales'
        );
        $this->assertResponseCode(403);

        $response = $this->clientJsonRequest(
            $departmentManagerOtherStoreToken,
            'GET',
            '/api/1/stores/' . $storeId . '/reports/grossSales'
        );
        $this->assertResponseCode(403);

        $response = $this->clientJsonRequest(
            $administratorToken,
            'GET',
            '/api/1/stores/' . $storeId . '/reports/grossSales'
        );
        $this->assertResponseCode(403);
    }

    public function testGetStoreGrossSalesReportsDiffs()
    {
        $storeId = $this->factory->getStore();
        $accessToken = $this->factory->authAsStoreManager($storeId);

        $product1Id = $this->createProduct('1');
        $product2Id = $this->createProduct('2');
        $product3Id = $this->createProduct('3');

        $sales = array(
            array(
                'storeId' => $storeId,
                'createdDate' => "7:25",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),
            array(
                'storeId' => $storeId,
                'createdDate' => "-1 days 8:01",
                'sumTotal' => 325.74,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 5,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 1,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 2,
                        'price' => 43.55,
                    ),
                ),
            ),
            array(
                'storeId' => $storeId,
                'createdDate' => "-1 week 9:01",
                'sumTotal' => 846.92,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 10,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),
        );
        $this->factory->createSales($sales);

        $storeGrossSalesReportService = $this->getGrossSalesReportService();

        $storeGrossSalesReportService->recalculateStoreGrossSalesReport();

        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $storeId . '/reports/grossSales',
            null,
            array('time' => date('c', strtotime("10:35:47")))
        );

        $this->assertResponseCode(200);

        $expected = array(
            'today' => array(
                'now' => array(
                    'date' => date(DateTime::ISO8601, strtotime("10:00")),
                    'value' => 603.53,
                )
            ),
            'yesterday' => array(
                'now' => array(
                    'date' => date(DateTime::ISO8601, strtotime("-1 day 10:00")),
                    'value' => 325.74,
                    'diff' => 85.28,
                ),
                'dayEnd' => array(
                    'date' => date(DateTime::ISO8601, strtotime("-1 day 23:59:59")),
                    'value' => 325.74,
                ),
            ),
            'weekAgo' => array(
                'now' => array(
                    'date' => date(DateTime::ISO8601, strtotime("-7 day 10:00")),
                    'value' => 846.92,
                    'diff' => -28.74
                ),
                'dayEnd' => array(
                    'date' => date(DateTime::ISO8601, strtotime("-7 day 23:59:59")),
                    'value' => 846.92,
                ),
            ),
        );

        $this->assertSame($expected, $response);
    }

    public function testGetStoreGrossSalesByHour()
    {
        $storeId = $this->factory->getStore();
        $accessToken = $this->factory->authAsStoreManager($storeId);

        $storeOtherId = $this->factory->getStore("other");

        $product1Id = $this->createProduct('1');
        $product2Id = $this->createProduct('2');
        $product3Id = $this->createProduct('3');

        $sales = array(
            array(
                'storeId' => $storeId,
                'createdDate' => "8:01",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),
            array(
                'storeId' => $storeId,
                'createdDate' => "9:01",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),
            array(
                'storeId' => $storeId,
                'createdDate' => "10:01",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),
            array(
                'storeId' => $storeId,
                'createdDate' => "11:01",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),

            array(
                'storeId' => $storeId,
                'createdDate' => '-1 days 8:01',
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),
            array(
                'storeId' => $storeId,
                'createdDate' => "-1 days 9:01",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),
            array(
                'storeId' => $storeId,
                'createdDate' => "-1 days 10:01",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),

            array(
                'storeId' => $storeId,
                'createdDate' => "-7 days 8:01",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),
            array(
                'storeId' => $storeId,
                'createdDate' => "-7 days 9:01",
                'sumTotal' => 705.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.00
                    ),
                ),
            ),
            array(
                'storeId' => $storeId,
                'createdDate' => "-7 days 10:01",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),
        );

        $this->factory->createSales($sales);


        $salesInOtherStore = array(
            array(
                'storeId' => $storeOtherId,
                'createdDate' => "8:01",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),
        );

        $this->factory->createSales($salesInOtherStore);


        $storeGrossSalesReportService = $this->getGrossSalesReportService();

        $storeGrossSalesReportService->recalculateStoreGrossSalesReport();

        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $storeId . '/reports/grossSalesByHours',
            null,
            array('time' => date('c', strtotime("10:35:47")))
        );

        $this->assertResponseCode(200);

        $expectedYesterday = $expectedWeekAgo = array();

        for ($i = 0; $i <= 7; $i++) {
            $expectedYesterday[$i] = array(
                'dayHour' => date(DateTime::ISO8601, strtotime("-1 day 0{$i}:00")),
                'runningSum' => 0,
                'hourSum' => 0,
            );
            $expectedWeekAgo[$i] = array(
                'dayHour' => date(DateTime::ISO8601, strtotime("-1 week 0{$i}:00")),
                'runningSum' => 0,
                'hourSum' => 0,
            );
        }
        $expectedYesterday += array(
            8 => array(
                'dayHour' => date(DateTime::ISO8601, strtotime("-1 day 08:00")),
                'runningSum' => 603.53,
                'hourSum' => 603.53,
            ),
            9 => array(
                'dayHour' => date(DateTime::ISO8601, strtotime("-1 day 09:00")),
                'runningSum' => 1207.06,
                'hourSum' => 603.53,
            ),
        );
        $expectedWeekAgo += array(
            8 => array(
                'dayHour' => date(DateTime::ISO8601, strtotime("-1 week 08:00")),
                'runningSum' => 603.53,
                'hourSum' => 603.53,
            ),
            9 => array(
                'dayHour' => date(DateTime::ISO8601, strtotime("-1 week 09:00")),
                'runningSum' => 1309.06,
                'hourSum' => 705.53,
            ),
        );

        $expected = array(
            'today' => array(
                0 => array(
                    'dayHour' => date(DateTime::ISO8601, strtotime("00:00")),
                    'runningSum' => 0,
                    'hourSum' => 0,
                ),
                1 => array(
                    'dayHour' => date(DateTime::ISO8601, strtotime("01:00")),
                    'runningSum' => 0,
                    'hourSum' => 0,
                ),
                2 => array(
                    'dayHour' => date(DateTime::ISO8601, strtotime("02:00")),
                    'runningSum' => 0,
                    'hourSum' => 0,
                ),
                3 => array(
                    'dayHour' => date(DateTime::ISO8601, strtotime("03:00")),
                    'runningSum' => 0,
                    'hourSum' => 0,
                ),
                4 => array(
                    'dayHour' => date(DateTime::ISO8601, strtotime("04:00")),
                    'runningSum' => 0,
                    'hourSum' => 0,
                ),
                5 => array(
                    'dayHour' => date(DateTime::ISO8601, strtotime("05:00")),
                    'runningSum' => 0,
                    'hourSum' => 0,
                ),
                6 => array(
                    'dayHour' => date(DateTime::ISO8601, strtotime("06:00")),
                    'runningSum' => 0,
                    'hourSum' => 0,
                ),
                7 => array(
                    'dayHour' => date(DateTime::ISO8601, strtotime("07:00")),
                    'runningSum' => 0,
                    'hourSum' => 0,
                ),
                8 => array(
                    'dayHour' => date(DateTime::ISO8601, strtotime("08:00")),
                    'runningSum' => 603.53,
                    'hourSum' => 603.53,
                ),
                9 => array(
                    'dayHour' => date(DateTime::ISO8601, strtotime("09:00")),
                    'runningSum' => 1207.06,
                    'hourSum' => 603.53,
                ),
            ),
            'yesterday' => $expectedYesterday,
            'weekAgo' => $expectedWeekAgo,
        );

        $this->assertEquals($expected, $response);
    }

    public function testAccessGetStoreGrossSalesReportByHours()
    {
        $storeId = $this->factory->getStore();
        $storeManagerToken = $this->factory->authAsStoreManager($storeId);
        $departmentManagerToken = $this->factory->authAsDepartmentManager($storeId);
        $storeManagerOtherStoreToken = $this->factory->authAsRole(User::ROLE_STORE_MANAGER);
        $commercialManagerToken = $this->factory->authAsRole(User::ROLE_COMMERCIAL_MANAGER);
        $departmentManagerOtherStoreToken = $this->factory->authAsRole(User::ROLE_DEPARTMENT_MANAGER);
        $administratorToken = $this->factory->authAsRole(User::ROLE_ADMINISTRATOR);


        $response = $this->clientJsonRequest(
            $storeManagerToken,
            'GET',
            '/api/1/stores/' . $storeId . '/reports/grossSalesByHours'
        );
        $this->assertResponseCode(200);

        $response = $this->clientJsonRequest(
            $departmentManagerToken,
            'GET',
            '/api/1/stores/' . $storeId . '/reports/grossSalesByHours'
        );
        $this->assertResponseCode(403);

        $response = $this->clientJsonRequest(
            $storeManagerOtherStoreToken,
            'GET',
            '/api/1/stores/' . $storeId . '/reports/grossSalesByHours'
        );
        $this->assertResponseCode(403);

        $response = $this->clientJsonRequest(
            $commercialManagerToken,
            'GET',
            '/api/1/stores/' . $storeId . '/reports/grossSalesByHours'
        );
        $this->assertResponseCode(403);

        $response = $this->clientJsonRequest(
            $departmentManagerOtherStoreToken,
            'GET',
            '/api/1/stores/' . $storeId . '/reports/grossSalesByHours'
        );
        $this->assertResponseCode(403);

        $response = $this->clientJsonRequest(
            $administratorToken,
            'GET',
            '/api/1/stores/' . $storeId . '/reports/grossSalesByHours'
        );
        $this->assertResponseCode(403);
    }

    public function testGetStoreGrossSalesByHourEmptyYesterday()
    {
        $storeId = $this->factory->getStore();
        $accessToken = $this->factory->authAsStoreManager($storeId);

        $storeOtherId = $this->factory->getStore("other");

        $product1Id = $this->createProduct('1');
        $product2Id = $this->createProduct('2');
        $product3Id = $this->createProduct('3');

        $sales = array(
            array(
                'storeId' => $storeId,
                'createdDate' => "8:01",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),
            array(
                'storeId' => $storeId,
                'createdDate' => "9:01",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),
            array(
                'storeId' => $storeId,
                'createdDate' => "10:01",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),
            array(
                'storeId' => $storeId,
                'createdDate' => "11:01",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),

            array(
                'storeId' => $storeId,
                'createdDate' => "-7 days 8:01",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),
            array(
                'storeId' => $storeId,
                'createdDate' => "-7 days 9:01",
                'sumTotal' => 705.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.00
                    ),
                ),
            ),
            array(
                'storeId' => $storeId,
                'createdDate' => "-7 days 10:01",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),
        );

        $this->factory->createSales($sales);


        $salesInOtherStore = array(
            array(
                'storeId' => $storeOtherId,
                'createdDate' => "8:01",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),
        );

        $this->factory->createSales($salesInOtherStore);


        $storeGrossSalesReportService = $this->getGrossSalesReportService();

        $storeGrossSalesReportService->recalculateStoreGrossSalesReport();

        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $storeId . '/reports/grossSalesByHours',
            null,
            array('time' => date('c', strtotime("10:35:47")))
        );

        $this->assertResponseCode(200);

        $expectedYesterday = $expectedWeekAgo = array();

        for ($i = 0; $i <= 7; $i++) {
            $expectedYesterday[$i] = array(
                'dayHour' => date(DateTime::ISO8601, strtotime("-1 day 0{$i}:00")),
                'runningSum' => 0,
                'hourSum' => 0,
            );
            $expectedWeekAgo[$i] = array(
                'dayHour' => date(DateTime::ISO8601, strtotime("-1 week 0{$i}:00")),
                'runningSum' => 0,
                'hourSum' => 0,
            );
        }
        $expectedYesterday += array(
            8 => array(
                'dayHour' => date(DateTime::ISO8601, strtotime("-1 day 08:00")),
                'runningSum' => 0,
                'hourSum' => 0,
            ),
            9 => array(
                'dayHour' => date(DateTime::ISO8601, strtotime("-1 day 09:00")),
                'runningSum' => 0,
                'hourSum' => 0,
            ),
        );
        $expectedWeekAgo += array(
            8 => array(
                'dayHour' => date(DateTime::ISO8601, strtotime("-1 week 08:00")),
                'runningSum' => 603.53,
                'hourSum' => 603.53,
            ),
            9 => array(
                'dayHour' => date(DateTime::ISO8601, strtotime("-1 week 09:00")),
                'runningSum' => 1309.06,
                'hourSum' => 705.53,
            ),
        );

        $expected = array(
            'today' => array(
                0 => array(
                    'dayHour' => date(DateTime::ISO8601, strtotime("00:00")),
                    'runningSum' => 0,
                    'hourSum' => 0,
                ),
                1 => array(
                    'dayHour' => date(DateTime::ISO8601, strtotime("01:00")),
                    'runningSum' => 0,
                    'hourSum' => 0,
                ),
                2 => array(
                    'dayHour' => date(DateTime::ISO8601, strtotime("02:00")),
                    'runningSum' => 0,
                    'hourSum' => 0,
                ),
                3 => array(
                    'dayHour' => date(DateTime::ISO8601, strtotime("03:00")),
                    'runningSum' => 0,
                    'hourSum' => 0,
                ),
                4 => array(
                    'dayHour' => date(DateTime::ISO8601, strtotime("04:00")),
                    'runningSum' => 0,
                    'hourSum' => 0,
                ),
                5 => array(
                    'dayHour' => date(DateTime::ISO8601, strtotime("05:00")),
                    'runningSum' => 0,
                    'hourSum' => 0,
                ),
                6 => array(
                    'dayHour' => date(DateTime::ISO8601, strtotime("06:00")),
                    'runningSum' => 0,
                    'hourSum' => 0,
                ),
                7 => array(
                    'dayHour' => date(DateTime::ISO8601, strtotime("07:00")),
                    'runningSum' => 0,
                    'hourSum' => 0,
                ),
                8 => array(
                    'dayHour' => date(DateTime::ISO8601, strtotime("08:00")),
                    'runningSum' => 603.53,
                    'hourSum' => 603.53,
                ),
                9 => array(
                    'dayHour' => date(DateTime::ISO8601, strtotime("09:00")),
                    'runningSum' => 1207.06,
                    'hourSum' => 603.53,
                ),
            ),
            'yesterday' => $expectedYesterday,
            'weekAgo' => $expectedWeekAgo,
        );

        $this->assertEquals($expected, $response);
    }

    public function testGetStoreGrossSalesByHourEmptyAll()
    {
        $storeId = $this->factory->getStore();
        $accessToken = $this->factory->authAsStoreManager($storeId);

        $storeOtherId = $this->factory->getStore("other");

        $product1Id = $this->createProduct('1');
        $product2Id = $this->createProduct('2');
        $product3Id = $this->createProduct('3');


        $salesInOtherStore = array(
            array(
                'storeId' => $storeOtherId,
                'createdDate' => "8:01",
                'sumTotal' => 603.53,
                'positions' => array(
                    array(
                        'productId' => $product1Id,
                        'quantity' => 3,
                        'price' => 34.77
                    ),
                    array(
                        'productId' => $product2Id,
                        'quantity' => 3,
                        'price' => 64.79
                    ),
                    array(
                        'productId' => $product3Id,
                        'quantity' => 7,
                        'price' => 43.55,
                    ),
                ),
            ),
        );

        $this->factory->createSales($salesInOtherStore);


        $storeGrossSalesReportService = $this->getGrossSalesReportService();

        $storeGrossSalesReportService->recalculateStoreGrossSalesReport();

        $response = $this->clientJsonRequest(
            $accessToken,
            'GET',
            '/api/1/stores/' . $storeId . '/reports/grossSalesByHours',
            null,
            array('time' => date('c', strtotime("10:35:47")))
        );

        $this->assertResponseCode(200);

        $expectedYesterday = $expectedWeekAgo = $expectedToday = array();

        for ($i = 0; $i <= 9; $i++) {
            $expectedToday[$i] = array(
                'dayHour' => date(DateTime::ISO8601, strtotime("0{$i}:00")),
                'runningSum' => 0,
                'hourSum' => 0,
            );
            $expectedYesterday[$i] = array(
                'dayHour' => date(DateTime::ISO8601, strtotime("-1 day 0{$i}:00")),
                'runningSum' => 0,
                'hourSum' => 0,
            );
            $expectedWeekAgo[$i] = array(
                'dayHour' => date(DateTime::ISO8601, strtotime("-1 week 0{$i}:00")),
                'runningSum' => 0,
                'hourSum' => 0,
            );
        }

        $expected = array(
            'today' => $expectedToday,
            'yesterday' => $expectedYesterday,
            'weekAgo' => $expectedWeekAgo,
        );

        $this->assertEquals($expected, $response);
    }
}
