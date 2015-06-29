<?php

namespace Lighthouse\ReportsBundle\Tests\Command;

use Lighthouse\CoreBundle\Test\ContainerAwareTestCase;

class RecalculateReportsCommandTest extends ContainerAwareTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->clearMongoDb();
        $this->authenticateProject();
    }
    
    protected function prepareDataForProject2()
    {
        $factory = $this->factory('project2');

        $stores = $factory->store()->getStores(array('21', '22'));

        $catalogGroups = $factory->catalog()->getSubCategories(array('2.1', '2.2'));

        $product211 = $factory->catalog()->createProductByName('2.1.1', $catalogGroups['2.1']);
        $product222 = $factory->catalog()->createProductByName('2.2.2', $catalogGroups['2.2']);

        $factory
            ->invoice()
                ->createInvoice(array('date' => '-7 days'), $stores['21']->id)
                ->createInvoiceProduct($product211->id, 10, 20)
                ->createInvoiceProduct($product222->id, 5, 25)
            ->persist()
                ->createInvoice(array('date' => '-6 days'), $stores['22']->id)
                ->createInvoiceProduct($product211->id, 11, 21)
                ->createInvoiceProduct($product222->id, 6, 26)
            ->flush();

        $factory
            ->receipt()
                ->createSale($stores['21'], '-3 days 11:23')
                ->createReceiptProduct($product211->id, 1, 30)
                ->createReceiptProduct($product222->id, 2.5, 31.09)
            ->persist()
                ->createSale($stores['22'], '-2 days 11:23')
                ->createReceiptProduct($product211->id, 1, 30)
                ->createReceiptProduct($product222->id, 2.5, 31.09)
            ->flush();
    }

    protected function prepareDataForProject3()
    {
        $factory = $this->factory('project3');

        $stores = $factory->store()->getStores(array('31', '32', '33'));

        $catalogGroups = $factory->catalog()->getSubCategories(array('3.1', '3.2', '3.3'));

        $product311 = $factory->catalog()->createProductByName('3.1.1', $catalogGroups['3.1']);
        $product322 = $factory->catalog()->createProductByName('3.2.2', $catalogGroups['3.2']);
        $product333 = $factory->catalog()->createProductByName('3.3.3', $catalogGroups['3.3']);
        $product334 = $factory->catalog()->createProductByName('3.3.4', $catalogGroups['3.3']);

        $factory
            ->invoice()
                ->createInvoice(array('date' => '-7 days'), $stores['31']->id)
                ->createInvoiceProduct($product311->id, 10, 20)
                ->createInvoiceProduct($product322->id, 5, 25)
            ->persist()
                ->createInvoice(array('date' => '-6 days'), $stores['32']->id)
                ->createInvoiceProduct($product322->id, 11, 21)
                ->createInvoiceProduct($product333->id, 6, 26)
            ->persist()
                ->createInvoice(array('date' => '-4 days'), $stores['33']->id)
                ->createInvoiceProduct($product333->id, 11, 21)
                ->createInvoiceProduct($product334->id, 6, 26)
            ->flush();

        $factory
            ->receipt()
                ->createSale($stores['31'], '-3 days 11:23')
                ->createReceiptProduct($product311->id, 1, 30)
                ->createReceiptProduct($product322->id, 2.5, 31.09)
            ->persist()
                ->createSale($stores['32'], '-2 days 11:23')
                ->createReceiptProduct($product322->id, 1, 30)
                ->createReceiptProduct($product333->id, 2.5, 31.09)
            ->persist()
                ->createSale($stores['33'], '-2 days 11:23')
                ->createReceiptProduct($product333->id, 1, 30)
                ->createReceiptProduct($product334->id, 2.5, 31.09)
            ->persist()
                ->createSale($stores['33'], '-1 days 11:23')
                ->createReceiptProduct($product333->id, 2, 30)
            ->flush();
    }

    public function testExecute()
    {
        $this->prepareDataForProject2();
        $this->prepareDataForProject3();

        $tester = $this->createConsoleTester();
        $tester->runCommand('lighthouse:reports:recalculate');

        $this->assertEquals(0, $tester->getStatusCode());

        $expectedDisplay = <<<EOF
Recalculate reports started
Recalculate reports for project project1
Cost Of Goods
Gross Margin Sales
Products
Catalog Groups
Stores
Network
Cost of inventory
Stores
Gross Return
Recalculate reports for project project2
Cost Of Goods
....                                                 4 / 4
Gross Margin Sales
Products
....                                                 4 / 4
Catalog Groups
....                                                 4 / 4
Stores
..                                                   2 / 2
Network
..                                                   2 / 2
Cost of inventory
Stores
..                                                   2 / 2
Gross Return
Recalculate reports for project project3
Cost Of Goods
......                                               6 / 6
Gross Margin Sales
Products
.......                                              7 / 7
Catalog Groups
......                                               6 / 6
Stores
....                                                 4 / 4
Network
...                                                  3 / 3
Cost of inventory
Stores
...                                                  3 / 3
Gross Return
Recalculate reports finished

EOF;
        $this->assertEquals($expectedDisplay, $tester->getDisplay());
    }
}
