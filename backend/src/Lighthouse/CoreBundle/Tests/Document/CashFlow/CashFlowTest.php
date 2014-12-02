<?php

namespace Lighthouse\CoreBundle\Tests\Document\CashFlow;

use Lighthouse\CoreBundle\Document\CashFlow\CashFlow;
use Lighthouse\CoreBundle\Document\CashFlow\CashFlowRepository;
use Lighthouse\CoreBundle\Test\WebTestCase;

class CashFlowTest extends WebTestCase
{
    /**
     * @return CashFlowRepository
     */
    protected function getCashFlowRepository()
    {
        return $this->getContainer()->get('lighthouse.core.document.repository.cash_flow');
    }

    public function testAutoCreateCashFlowOnCreateAndEditInvoice()
    {
        $product = $this->createProduct();

        $invoice = $this->factory()
            ->invoice()
                ->createInvoice(array('paid' => true))
                ->createInvoiceProduct($product, 100, 7)
            ->flush();

        $cashFlowsCursor = $this->getCashFlowRepository()->findAll();
        $this->assertCount(1, $cashFlowsCursor);

        /** @var CashFlow $cashFlow */
        $cashFlow = $cashFlowsCursor->current();

        $this->assertTrue($cashFlow->amount->equals('700.00'), 'Amount not equals expected');
        $this->assertEquals('out', $cashFlow->direction);
        $this->assertEquals($invoice->date, $cashFlow->date);

        $this->factory()->invoice()
            ->editInvoice($invoice->id, array('paid' => false))
            ->flush();

        $cashFlowsCursor = $this->getCashFlowRepository()->findAll();
        $this->assertCount(0, $cashFlowsCursor);

        $this->factory()->invoice()
            ->editInvoice($invoice->id, array('paid' => true))
            ->flush();

        $cashFlowsCursor = $this->getCashFlowRepository()->findAll();
        $this->assertCount(1, $cashFlowsCursor);

        /** @var CashFlow $cashFlow */
        $cashFlow = $cashFlowsCursor->current();

        $this->assertTrue($cashFlow->amount->equals('700.00'), 'Amount not equals expected');
        $this->assertEquals('out', $cashFlow->direction);
        $this->assertEquals($invoice->date, $cashFlow->date);
    }

    public function testAutoChangeAmountCashFlowOnEditInvoice()
    {
        $product = $this->createProduct();

        $invoice = $this->factory()
            ->invoice()
                ->createInvoice(array('paid' => true))
                ->createInvoiceProduct($product, 100, 7)
            ->flush();

        $cashFlowsCursor = $this->getCashFlowRepository()->findAll();
        $this->assertCount(1, $cashFlowsCursor);

        /** @var CashFlow $cashFlow */
        $cashFlow = $cashFlowsCursor->current();

        $this->assertTrue($cashFlow->amount->equals('700.00'), 'Amount not equals expected');
        $this->assertEquals('out', $cashFlow->direction);
        $this->assertEquals($invoice->date, $cashFlow->date);

        $editedInvoice = $this->factory()
            ->invoice()
                ->editInvoice($invoice->id)
                ->editInvoiceProduct(0, $product, 50, 5)
            ->flush();

        $cashFlowsCursor = $this->getCashFlowRepository()->findAll();
        $this->assertCount(1, $cashFlowsCursor);

        /** @var CashFlow $cashFlow */
        $cashFlow = $cashFlowsCursor->current();

        $this->assertTrue($cashFlow->amount->equals('250.00'), 'Amount not equals expected');
        $this->assertEquals('out', $cashFlow->direction);
        $this->assertEquals($editedInvoice->date, $cashFlow->date);
    }

    public function testAutoCreateCashFlowOnCreateAndEditSupplierReturn()
    {
        $product = $this->createProduct();

        $supplierReturn = $this->factory()
            ->supplierReturn()
            ->createSupplierReturn(null, null, null, true)
            ->createSupplierReturnProduct($product, 100, 7)
            ->flush();

        $cashFlowsCursor = $this->getCashFlowRepository()->findAll();
        $this->assertCount(1, $cashFlowsCursor);

        /** @var CashFlow $cashFlow */
        $cashFlow = $cashFlowsCursor->current();

        $this->assertTrue($cashFlow->amount->equals('700.00'), 'Amount not equals expected');
        $this->assertEquals('in', $cashFlow->direction);
        $this->assertEquals($supplierReturn->date, $cashFlow->date);

        $this->factory()->supplierReturn()
            ->editSupplierReturn($supplierReturn->id, null, null, null, false)
            ->flush();

        $cashFlowsCursor = $this->getCashFlowRepository()->findAll();
        $this->assertCount(0, $cashFlowsCursor);

        $this->factory()->supplierReturn()
            ->editSupplierReturn($supplierReturn->id, null, null, null, true)
            ->flush();

        $cashFlowsCursor = $this->getCashFlowRepository()->findAll();
        $this->assertCount(1, $cashFlowsCursor);

        /** @var CashFlow $cashFlow */
        $cashFlow = $cashFlowsCursor->current();

        $this->assertTrue($cashFlow->amount->equals('700.00'), 'Amount not equals expected');
        $this->assertEquals('in', $cashFlow->direction);
        $this->assertEquals($supplierReturn->date, $cashFlow->date);
    }

    public function testAutoChangeAmountCashFlowOnEditSupplierReturn()
    {
        $product = $this->createProduct();

        $supplierReturn = $this->factory()
            ->supplierReturn()
            ->createSupplierReturn(null, null, null, true)
            ->createSupplierReturnProduct($product, 100, 7)
            ->flush();

        $cashFlowsCursor = $this->getCashFlowRepository()->findAll();
        $this->assertCount(1, $cashFlowsCursor);

        /** @var CashFlow $cashFlow */
        $cashFlow = $cashFlowsCursor->current();

        $this->assertTrue($cashFlow->amount->equals('700.00'), 'Amount not equals expected');
        $this->assertEquals('in', $cashFlow->direction);
        $this->assertEquals($supplierReturn->date, $cashFlow->date);

        $editedSupplierReturn = $this->factory()
            ->supplierReturn()
            ->editSupplierReturn($supplierReturn->id)
            ->editSupplierReturnProduct(0, $product, 50, 5)
            ->flush();

        $cashFlowsCursor = $this->getCashFlowRepository()->findAll();
        $this->assertCount(1, $cashFlowsCursor);

        /** @var CashFlow $cashFlow */
        $cashFlow = $cashFlowsCursor->current();

        $this->assertTrue($cashFlow->amount->equals('250.00'), 'Amount not equals expected');
        $this->assertEquals('in', $cashFlow->direction);
        $this->assertEquals($editedSupplierReturn->date, $cashFlow->date);
    }
}
