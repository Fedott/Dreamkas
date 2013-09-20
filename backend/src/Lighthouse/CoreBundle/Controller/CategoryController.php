<?php

namespace Lighthouse\CoreBundle\Controller;

use FOS\RestBundle\View\View;
use Lighthouse\CoreBundle\Document\Classifier\Category\Category;
use Lighthouse\CoreBundle\Document\Classifier\Category\CategoryCollection;
use Lighthouse\CoreBundle\Document\Classifier\Category\CategoryRepository;
use Lighthouse\CoreBundle\Document\Store\Store;
use Lighthouse\CoreBundle\Form\CategoryType;
use Lighthouse\CoreBundle\Document\Classifier\Group\Group;
use JMS\DiExtraBundle\Annotation as DI;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\SecureParam;

class CategoryController extends AbstractRestController
{
    /**
     * @DI\Inject("lighthouse.core.document.repository.classifier.category")
     * @var CategoryRepository
     */
    protected $documentRepository;

    /**
     * @return CategoryType
     */
    protected function getDocumentFormType()
    {
        return new CategoryType();
    }

    /**
     * @param Request $request
     * @return View|Category
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
     * @param Category $category
     * @return View|Category
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
     * @param Store $store
     * @param Category $category
     * @return Category
     * @Secure(roles="ROLE_STORE_MANAGER,ROLE_DEPARTMENT_MANAGER")
     * @SecureParam(name="store", permissions="ACL_STORE_MANAGER,ACL_DEPARTMENT_MANAGER")
     * @ApiDoc(
     *      resource=true
     * )
     */
    public function getStoreCategoryAction(Store $store, Category $category)
    {
        return $category;
    }

    /**
     * @param \Lighthouse\CoreBundle\Document\Classifier\Group\Group $group
     * @return CategoryCollection
     * @Secure(roles="ROLE_COMMERCIAL_MANAGER")
     * @ApiDoc
     */
    public function getGroupCategoriesAction(Group $group)
    {
        $cursor = $this->documentRepository->findByGroup($group->id);
        return new CategoryCollection($cursor);
    }


    /**
     * @param Store $store
     * @param Group $group
     * @return CategoryCollection
     * @Secure(roles="ROLE_STORE_MANAGER,ROLE_DEPARTMENT_MANAGER")
     * @SecureParam(name="store", permissions="ACL_STORE_MANAGER,ACL_DEPARTMENT_MANAGER")
     * @ApiDoc
     */
    public function getStoreGroupCategoriesAction(Store $store, Group $group)
    {
        $cursor = $this->documentRepository->findByGroup($group->id);
        return new CategoryCollection($cursor);
    }

    /**
     * @param \Lighthouse\CoreBundle\Document\Classifier\Category\Category $category
     * @return null
     * @Secure(roles="ROLE_COMMERCIAL_MANAGER")
     * @ApiDoc
     */
    public function deleteCategoriesAction(Category $category)
    {
        return $this->processDelete($category);
    }
}
