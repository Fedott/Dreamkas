<?php

namespace Lighthouse\CoreBundle\Controller;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\ODM\MongoDB\LoggableCursor;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Lighthouse\CoreBundle\Document\Product;
use Lighthouse\CoreBundle\Document\ProductCollection;
use Lighthouse\CoreBundle\Form\ProductType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends FOSRestController
{
    /**
     * @DI\Inject("doctrine_mongodb")
     * @var ManagerRegistry
     */
    protected $odm;

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getProductRepository()
    {
        return $this->odm->getRepository("LighthouseCoreBundle:Product");
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \FOS\RestBundle\View\View|\Lighthouse\CoreBundle\Document\Product
     *
     * @Rest\View(statusCode=201)
     */
    public function postProductsAction(Request $request)
    {
        return $this->processForm($request, new Product());
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $id
     * @return \FOS\RestBundle\View\View|\Lighthouse\CoreBundle\Document\Product
     *
     * @Rest\View(statusCode=204)
     */
    public function putProductsAction(Request $request, $id)
    {
        $product = $this->findProduct($id);

        return $this->processForm($request, $product);
    }

    /**
     * @param string $id
     * @return \Lighthouse\CoreBundle\Document\Product
     */
    public function getProductAction($id)
    {
        return $this->findProduct($id);
    }

    /**
     * @return \Lighthouse\CoreBundle\Document\ProductCollection
     */
    public function getProductsAction()
    {
        /* @var LoggableCursor $cursor */
        $cursor = $this->getProductRepository()->findAll();
        $collection = new ProductCollection($cursor);
        return $collection;
    }

    /**
     * @param string $id
     * @return \Lighthouse\CoreBundle\Document\Product
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function findProduct($id)
    {
        $product = $this->getProductRepository()->find($id);
        if (!$product instanceof Product) {
            throw new NotFoundHttpException('Product not found');
        }
        return $product;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Lighthouse\CoreBundle\Document\Product $product
     * @return \FOS\RestBundle\View\View|\Lighthouse\CoreBundle\Document\Product
     */
    protected function processForm(Request $request, Product $product)
    {
        $productType = new ProductType();

        $form = $this->createForm($productType, $product);
        $form->bind($request);

        if ($form->isValid()) {
            $this->odm->getManager()->persist($product);
            $this->odm->getManager()->flush();
            $product->purchasePrice;
            return $product;
        } else {
            return View::create($form, 400);
        }
    }
}
