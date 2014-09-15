<?php

namespace Lighthouse\CoreBundle\Tests\Validator\Constraints\StoreProduct;

use Lighthouse\CoreBundle\Document\Product\Product;
use Lighthouse\CoreBundle\Document\Product\Store\StoreProduct;
use Lighthouse\CoreBundle\Tests\Validator\Constraints\ConstraintTestCase;
use Lighthouse\CoreBundle\Validator\Constraints\StoreProduct\RetailPrice;

class RetailPriceValidatorTest extends ConstraintTestCase
{
    public function testValidateNoProductRetailsEmptyRetailPrice()
    {
        $product = new Product();
        $storeProduct = new StoreProduct();
        $storeProduct->product = $product;
        $storeProduct->retailPricePreference = Product::RETAIL_PRICE_PREFERENCE_PRICE;

        $constraint = new RetailPrice();

        $violationsList = $this->getValidator()->validate($storeProduct, $constraint, null);
        $this->assertCount(0, $violationsList);
    }

    public function testValidateNoProductRetailsEmptyRetailMarkup()
    {
        $product = new Product();
        $storeProduct = new StoreProduct();
        $storeProduct->product = $product;
        $storeProduct->retailPricePreference = Product::RETAIL_PRICE_PREFERENCE_MARKUP;

        $constraint = new RetailPrice();
        $violationsList = $this->getValidator()->validate($storeProduct, $constraint, null);
        $this->assertCount(0, $violationsList);
    }
}
