<?php

namespace Lighthouse\IntegrationBundle\Tests\OneC\Import\Invoices;

use Lighthouse\CoreBundle\Document\StockMovement\Invoice\InvoiceRepository;
use Lighthouse\CoreBundle\Document\StockMovement\StockMovementProductRepository;
use Lighthouse\CoreBundle\Test\TestOutput;
use Lighthouse\IntegrationBundle\OneC\Import\Invoices\InvoicesImporter;
use Lighthouse\IntegrationBundle\Test\WebTestCase;

class InvoicesImporterTest extends WebTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->authenticateProject();
    }

    /**
     * @param string $filePath
     * @param int $batchSize
     * @return TestOutput
     */
    protected function import($filePath, $batchSize = 5)
    {
        /* @var InvoicesImporter $importer */
        $importer = $this->getContainer()->get('lighthouse.integration.onec.import.invoices.importer');
        $output = new TestOutput();
        $importer->import($filePath, $batchSize, $output);

        return $output;
    }

    /**
     * @param array $storeInvoiceCount
     */
    protected function assertStoreInvoiceCount(array $storeInvoiceCount)
    {
        /* @var InvoiceRepository $invoiceRepository */
        $invoiceRepository = $this->getContainer()->get('lighthouse.core.document.repository.stock_movement.invoice');
        foreach ($storeInvoiceCount as $storeId => $count) {
            $invoices = $invoiceRepository->findByStore($storeId);
            $this->assertEquals($count, $invoices->count());
        }
    }

    /**
     * @param array $storeInvoiceProductCount
     */
    protected function assertStoreInvoiceProductCount(array $storeInvoiceProductCount)
    {
        foreach ($storeInvoiceProductCount as $storeId => $count) {
            $invoiceProducts = $this->getInvoiceProductRepository()->findByStoreId($storeId);
            $this->assertEquals($count, $invoiceProducts->count());
        }
    }

    /**
     * @expectedExceptionMessage Store with address
     */
    public function testImportStoreNotFound()
    {
        $filePath = $this->getFixtureFilePath('OneC/Import/Invoices/amn.csv');

        $this->authenticateProject();

        $output = $this->import($filePath);

        $display = $output->getDisplay();
        $this->assertContains(
            "[Lighthouse\\CoreBundle\\Exception\\RuntimeException] Store with address 'Магазин Галерея' not found",
            $display
        );
        $this->assertContains(
            "[Lighthouse\\CoreBundle\\Exception\\RuntimeException] Store with address 'СитиМолл' not found",
            $display
        );
        $this->assertContains(
            "[Lighthouse\\CoreBundle\\Exception\\RuntimeException] Store with address 'ТК Невский 104' not found",
            $display
        );
        $this->assertContains(
            "[Lighthouse\\CoreBundle\\Exception\\RuntimeException] Store with address 'ТК НОРД 1-44' not found",
            $display
        );
        $this->assertContains(
            "[Lighthouse\\CoreBundle\\Exception\\RuntimeException] Store with address 'ТК Пик' not found",
            $display
        );
    }

    public function testImportNoStoreInInvoiceRow()
    {
        $this->markTestSkipped('Supplier create');
        $storeId1 = $this->factory()->store()->getStoreId(1, 'Авиаконструкторов 2');
        $storeId2 = $this->factory()->store()->getStoreId(2, 'Есенина 1');
        $storeId3 = $this->factory()->store()->getStoreId(3, 'Металлистов, 116 (МЕ)');

        $this->factory()->catalog()->getProductByNames(
            array(
                'Ц0000001371',
                'Ц0000001313',
                'Ц0000001852',
                'Ц0000001417',
                'Ц0000000235',
                'ЕС000000107',
                'РТ000000035',
                'МЕ000000036',
                'АВ000000221',
                'Ц0000001937',
                'АВ000000259',
                'МЕ000000364',
                'МЕ000000365',
                'ЕС000000197',
                'ЕС000000221',
                'Ц0000002019',
                'АВ000000297',
                'МЕ000000084',
                'МЕ000000086',
                'МЕ000000085',
                'Ц0000001637',
                'Ц0000001640',
            )
        );

        $filePath = $this->getFixtureFilePath('Integration/OneC/Import/Invoices/food.csv');

        $this->import($filePath, 100);

        $storeInvoiceCount = array(
            $storeId1 => 2,
            $storeId2 => 2,
            $storeId3 => 3,
        );
        $this->assertStoreInvoiceCount($storeInvoiceCount);

        $storeInvoiceProductCount = array(
            $storeId1 => 7,
            $storeId2 => 4,
            $storeId3 => 11,
        );
        $this->assertStoreInvoiceProductCount($storeInvoiceProductCount);
    }

    /**
     * @return StockMovementProductRepository
     */
    protected function getInvoiceProductRepository()
    {
        return $this->getContainer()->get('lighthouse.core.document.repository.stock_movement.invoice_product');
    }
}
