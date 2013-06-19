<?php

namespace Lighthouse\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Lighthouse\CoreBundle\Document\Invoice\Invoice;
use Lighthouse\CoreBundle\Document\InvoiceProduct\InvoiceProduct;
use Lighthouse\CoreBundle\Document\InvoiceProduct\InvoiceProductCollection;
use Lighthouse\CoreBundle\Document\InvoiceProduct\InvoiceProductRepository;
use Lighthouse\CoreBundle\Document\Invoice\InvoiceRepository;
use Lighthouse\CoreBundle\Form\InvoiceProductType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use JMS\DiExtraBundle\Annotation as DI;

class InvoiceProductController extends AbstractRestController
{
    /**
     * @DI\Inject("lighthouse.core.document.repository.invoice_product")
     * @var InvoiceProductRepository
     */
    protected $documentRepository;

    /**
     * @DI\Inject("lighthouse.core.document.repository.invoice")
     * @var InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * @return InvoiceProductType
     */
    protected function getDocumentFormType()
    {
        return new InvoiceProductType();
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $invoiceId
     * @return \FOS\RestBundle\View\View|Invoice
     *
     * @Rest\View(statusCode=201)
     */
    public function postProductsAction(Request $request, $invoiceId)
    {
        $invoice = $this->findInvoice($invoiceId);
        $invoiceProduct = new InvoiceProduct();
        $invoiceProduct->invoice = $invoice;
        return $this->processForm($request, $invoiceProduct);
    }

    /**
     * @param Request $request
     * @param string $invoiceId
     * @param string $invoiceProductId
     *
     * @return \FOS\RestBundle\View\View|InvoiceProduct
     *
     * @Rest\View(statusCode=200)
     */
    public function putProductAction(Request $request, $invoiceId, $invoiceProductId)
    {
        $invoiceProduct = $this->findInvoiceProduct($invoiceProductId, $invoiceId);
        return $this->processForm($request, $invoiceProduct);
    }

    /**
     * @param string $invoiceId
     * @param string $invoiceProductId
     * @return InvoiceProductCollection
     */
    public function getProductAction($invoiceId, $invoiceProductId)
    {
        return $this->findInvoiceProduct($invoiceProductId, $invoiceId);
    }

    /**
     * @param string $invoiceId
     * @return InvoiceProductCollection
     */
    public function getProductsAction($invoiceId)
    {
        $invoice = $this->findInvoice($invoiceId);
        $invoiceProducts = $this->getDocumentRepository()->findByInvoice($invoice->id);
        return new InvoiceProductCollection($invoiceProducts);
    }

    /**
     * @param string $invoiceId
     * @param string $invoiceProductId
     * @Rest\View(statusCode=204)
     */
    public function deleteProductAction($invoiceId, $invoiceProductId)
    {
        $invoiceProduct = $this->findInvoiceProduct($invoiceProductId, $invoiceId);
        $this->getDocumentRepository()->getDocumentManager()->remove($invoiceProduct);
        $this->getDocumentRepository()->getDocumentManager()->flush();
    }

    /**
     * @param string $invoiceId
     * @return Invoice
     * @throws NotFoundHttpException
     */
    protected function findInvoice($invoiceId)
    {
        $invoice = $this->invoiceRepository->find($invoiceId);
        if (null === $invoice) {
            throw new NotFoundHttpException("Invoice not found");
        }
        return $invoice;
    }

    /**
     * @param string $invoiceProductId
     * @param string $invoiceId
     * @throws NotFoundHttpException
     * @return InvoiceProduct
     */
    protected function findInvoiceProduct($invoiceProductId, $invoiceId)
    {
        $invoiceProduct = $this->getDocumentRepository()->find($invoiceProductId);
        if (null === $invoiceProduct) {
            throw new NotFoundHttpException("InvoiceProduct not found");
        } elseif ($invoiceProduct->invoice->id != $invoiceId) {
            throw new NotFoundHttpException("InvoiceProduct not found");
        }
        return $invoiceProduct;
    }
}
