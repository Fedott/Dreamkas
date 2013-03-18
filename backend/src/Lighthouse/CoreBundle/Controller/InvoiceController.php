<?php

namespace Lighthouse\CoreBundle\Controller;

use Lighthouse\CoreBundle\Document\Invoice;
use Lighthouse\CoreBundle\Form\InvoiceType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use JMS\DiExtraBundle\Annotation as DI;

class InvoiceController extends FOSRestController
{
    /**
     * @DI\Inject("doctrine_mongodb")
     * @var ManagerRegistry
     */
    protected $odm;

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getInvoiceRepository()
    {
        return $this->odm->getRepository("LighthouseCoreBundle:Invoice");
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \FOS\RestBundle\View\View|\Lighthouse\CoreBundle\Document\Invoice
     *
     * @Rest\View(statusCode=201)
     */
    public function postInvoicesAction(Request $request)
    {
        return $this->processForm($request, new Invoice());
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Lighthouse\CoreBundle\Document\Invoice $invoice
     * @return \FOS\RestBundle\View\View|\Lighthouse\CoreBundle\Document\Invoice
     */
    protected function processForm(Request $request, Invoice $invoice)
    {
        $invoiceType = new InvoiceType();

        $form = $this->createForm($invoiceType, $invoice);
        $form->bind($request);

        if ($form->isValid()) {
            $this->odm->getManager()->persist($invoice);
            $this->odm->getManager()->flush();
            return $invoice;
        } else {
            return View::create($form, 400);
        }
    }
}
