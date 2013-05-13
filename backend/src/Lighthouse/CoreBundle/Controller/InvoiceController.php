<?php

namespace Lighthouse\CoreBundle\Controller;

use Doctrine\ODM\MongoDB\LoggableCursor;
use Lighthouse\CoreBundle\Document\Invoice\Invoice;
use Lighthouse\CoreBundle\Document\Invoice\InvoiceCollection;
use Lighthouse\CoreBundle\Document\Invoice\InvoiceRepository;
use Lighthouse\CoreBundle\Form\InvoiceType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InvoiceController extends FOSRestController
{
    /**
     * @DI\Inject("lighthouse.core.document.repository.invoice")
     * @var InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \FOS\RestBundle\View\View|\Lighthouse\CoreBundle\Document\Invoice\Invoice
     *
     * @Rest\View(statusCode=201)
     */
    public function postInvoicesAction(Request $request)
    {
        return $this->processForm($request, new Invoice());
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $id
     * @return \FOS\RestBundle\View\View|\Lighthouse\CoreBundle\Document\Invoice\Invoice
     *
     * @Rest\View(statusCode=200)
     */
    public function putInvoicesAction(Request $request, $id)
    {
        $invoice = $this->findInvoice($id);
        return $this->processForm($request, $invoice);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Lighthouse\CoreBundle\Document\Invoice\Invoice $invoice
     * @return \FOS\RestBundle\View\View|\Lighthouse\CoreBundle\Document\Invoice\Invoice
     */
    protected function processForm(Request $request, Invoice $invoice)
    {
        $invoiceType = new InvoiceType();

        $form = $this->createForm($invoiceType, $invoice);
        $form->bind($request);

        if ($form->isValid()) {
            $this->invoiceRepository->getDocumentManager()->persist($invoice);
            $this->invoiceRepository->getDocumentManager()->flush();
            return $invoice;
        } else {
            return View::create($form, 400);
        }
    }

    /**
     * @return \FOS\RestBundle\View\View|\Lighthouse\CoreBundle\Document\Invoice\InvoiceCollection
     */
    public function getInvoicesAction()
    {
        /* @var LoggableCursor $cursor */
        $cursor = $this->invoiceRepository->findAll();
        $collection = new InvoiceCollection($cursor);
        return $collection;
    }

    /**
     * @param int $id
     * @return \Lighthouse\CoreBundle\Document\Invoice\Invoice
     */
    public function getInvoiceAction($id)
    {
        return $this->findInvoice($id);
    }

    /**
     * @param string $id
     * @return Invoice
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function findInvoice($id)
    {
        $invoice = $this->invoiceRepository->find($id);
        if (!$invoice instanceof Invoice) {
            throw new NotFoundHttpException('Product not found');
        }
        return $invoice;
    }
}
