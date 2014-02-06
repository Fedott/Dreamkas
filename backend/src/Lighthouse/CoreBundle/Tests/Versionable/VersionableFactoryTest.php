<?php

namespace Lighthouse\CoreBundle\Tests\Versionable;

use Doctrine\ODM\MongoDB\UnitOfWork;
use Lighthouse\CoreBundle\Document\Classifier\SubCategory\SubCategory;
use Lighthouse\CoreBundle\Document\Product\Product;
use Lighthouse\CoreBundle\Document\Product\Version\ProductVersion;
use Lighthouse\CoreBundle\Test\ContainerAwareTestCase;
use Lighthouse\CoreBundle\Types\Numeric\Money;
use Lighthouse\CoreBundle\Versionable\VersionFactory;
use Lighthouse\CoreBundle\Versionable\VersionRepository;

class VersionableFactoryTest extends ContainerAwareTestCase
{
    public function testCreateVersionable()
    {
        $this->clearMongoDb();

        $product = $this->createProduct();

        $this->getDocumentManager()->persist($product);
        $this->getDocumentManager()->flush();

        /* @var VersionFactory $versionFactory */
        $versionFactory = $this->getContainer()->get('lighthouse.core.versionable.factory');
        /* @var ProductVersion $productVersion */
        $productVersion = $versionFactory->createDocumentVersion($product);

        $version = $productVersion->getVersion();

        $this->assertInstanceOf('Lighthouse\\CoreBundle\\Document\\Product\\Version\\ProductVersion', $productVersion);
        $this->assertEquals($product->name, $productVersion->name);
        $this->assertEquals($product->units, $productVersion->units);
        $this->assertEquals($product->barcode, $productVersion->barcode);
        $this->assertNotNull($productVersion->getVersion());
        $this->assertSame($product, $productVersion->getObject());

        $this->getDocumentManager()->persist($productVersion);
        $this->getDocumentManager()->flush();

        $this->assertEquals($version, $productVersion->getVersion());
    }

    public function testFindByDocument()
    {
        $this->clearMongoDb();

        $product = $this->createProduct();

        $this->getDocumentManager()->persist($product);
        $this->getDocumentManager()->flush();

        /* @var VersionRepository $productVersionRepo */
        $productVersionRepo = $this->getContainer()->get('lighthouse.core.document.repository.product_version');
        $productVersion = $productVersionRepo->findOrCreateByDocument($product);

        $state = $this->getDocumentManager()->getUnitOfWork()->getDocumentState($productVersion);
        $this->assertEquals(UnitOfWork::STATE_MANAGED, $state);

        $this->getDocumentManager()->persist($productVersion);
        $this->getDocumentManager()->flush();

        $this->getDocumentManager()->clear();

        $foundProductVersion = $productVersionRepo->findOrCreateByDocument($product);

        $this->assertEquals($productVersion->getVersion(), $foundProductVersion->getVersion());
        $this->assertNotSame($productVersion, $foundProductVersion);
    }

    /**
     * @return Product
     */
    protected function createProduct()
    {
        $product = new Product();
        $product->name = 'Кефир "Веселый Молочник" 1% 950гр';
        $product->units = 'gr';
        $product->barcode = '4607025392408';
        $product->purchasePrice = new Money(30.48);
        $product->sku = 'КЕФИР "ВЕСЕЛЫЙ МОЛОЧНИК" 1% КАРТОН УПК. 950ГР';
        $product->vat = 10;
        $product->vendor = 'Вимм-Билль-Данн';
        $product->vendorCountry = 'Россия';
        $product->info = 'Классный кефирчик, употребляю давно, всем рекомендую для поднятия тонуса';

        $roundingManager = $this->getContainer()->get('lighthouse.core.rounding.manager');
        $rounding = $roundingManager->findDefault();

        $subCategory = new SubCategory();
        $subCategory->name = 'Кефир 1%';
        $subCategory->rounding = $rounding;

        $product->subCategory = $subCategory;
        return $product;
    }
}
