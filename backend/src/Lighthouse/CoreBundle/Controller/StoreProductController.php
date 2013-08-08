<?php

namespace Lighthouse\CoreBundle\Controller;

use Lighthouse\CoreBundle\Document\Product\Product;
use Lighthouse\CoreBundle\Document\Product\Store\StoreProductRepository;
use Lighthouse\CoreBundle\Document\Store\Store;
use Lighthouse\CoreBundle\Exception\RuntimeException;
use Lighthouse\CoreBundle\Form\StoreProductType;
use Symfony\Component\Form\AbstractType;
use JMS\DiExtraBundle\Annotation as DI;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class StoreProductController extends AbstractRestController
{
    /**
     * @DI\Inject("lighthouse.core.document.repository.store_product")
     * @var StoreProductRepository
     */
    protected $documentRepository;

    /**
     * @return StoreProductType
     */
    protected function getDocumentFormType()
    {
        return new StoreProductType();
    }

    /**
     * @param Store $store
     * @param Product $product
     * @ApiDoc(
     *      resource=true
     * )
     */
    public function getStoreProductAction(Store $store, Product $product)
    {
        return $this->findStoreProduct($store, $product);
    }

    /**
     * @param Store $store
     * @param Product $product
     * @param Request $request
     * @return \FOS\RestBundle\View\View|\Lighthouse\CoreBundle\Document\AbstractDocument
     * @ApiDoc
     */
    public function putStoreProductAction(Store $store, Product $product, Request $request)
    {
        $storeProduct = $this->findStoreProduct($store, $product);
        return $this->processForm($request, $storeProduct);
    }

    /**
     * @param Store $store
     * @param Product $product
     * @return \Lighthouse\CoreBundle\Document\Product\Store\StoreProduct
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function findStoreProduct(Store $store, Product $product)
    {
        $storeProduct = $this->documentRepository->findOrCreateByStoreProduct($store, $product);
        if (!$storeProduct) {
            throw new NotFoundHttpException(sprintf('%s object not found.', 'StoreProduct'));
        }
        return $storeProduct;
    }
}
