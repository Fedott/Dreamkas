<?php

namespace Lighthouse\CoreBundle\Controller;

use Lighthouse\CoreBundle\Document\Category\Category;
use Lighthouse\CoreBundle\Document\Category\CategoryCollection;
use Lighthouse\CoreBundle\Document\Category\CategoryRepository;
use Lighthouse\CoreBundle\Document\Klass\KlassRepository;
use Lighthouse\CoreBundle\Form\CategoryType;
use Lighthouse\CoreBundle\Document\Klass\Klass;
use JMS\DiExtraBundle\Annotation as DI;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use JMS\SecurityExtraBundle\Annotation\Secure;

class CategoryController extends AbstractRestController
{
    /**
     * @DI\Inject("lighthouse.core.document.repository.category")
     * @var CategoryRepository
     */
    protected $documentRepository;

    /**
     * @DI\Inject("lighthouse.core.document.repository.klass")
     * @var KlassRepository
     */
    protected $klassRepository;

    /**
     * @return CategoryType
     */
    protected function getDocumentFormType()
    {
        return new CategoryType();
    }

    /**
     * @param Request $request
     * @return \FOS\RestBundle\View\View|Category
     * @Rest\View(statusCode=201)
     * @Secure(roles="ROLE_COMMERCIAL_MANAGER")
     * @ApiDoc(
     *      resource=true
     * )
     */
    public function postCategoriesAction(Request $request)
    {
        return $this->processPost($request);
    }

    /**
     * @param Request $request
     * @param \Lighthouse\CoreBundle\Document\Category\Category $category
     * @return \FOS\RestBundle\View\View|Category
     * @Secure(roles="ROLE_COMMERCIAL_MANAGER")
     * @ApiDoc
     */
    public function putCategoriesAction(Request $request, Category $category)
    {
        return $this->processForm($request, $category);
    }

    /**
     * @param Category $category
     * @return Category
     * @Secure(roles="ROLE_COMMERCIAL_MANAGER")
     * @ApiDoc
     */
    public function getCategoryAction(Category $category)
    {
        return $category;
    }

    /**
     * @param Klass $klass
     * @return CategoryCollection
     * @Secure(roles="ROLE_COMMERCIAL_MANAGER")
     * @ApiDoc
     */
    public function getKlassCategoriesAction(Klass $klass)
    {
        $cursor = $this->getDocumentRepository()->findByKlass($klass->id);
        $collection = new CategoryCollection($cursor);
        return $collection;
    }

    /**
     * @param \Lighthouse\CoreBundle\Document\Category\Category $category
     * @return null
     * @Secure(roles="ROLE_COMMERCIAL_MANAGER")
     * @ApiDoc
     */
    public function deleteCategoriesAction(Category $category)
    {
        return $this->processDelete($category);
    }
}
