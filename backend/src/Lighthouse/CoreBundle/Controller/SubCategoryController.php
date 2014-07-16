<?php

namespace Lighthouse\CoreBundle\Controller;

use Doctrine\ODM\MongoDB\Cursor;
use Lighthouse\CoreBundle\Document\Classifier\CatalogManager;
use Lighthouse\CoreBundle\Document\Classifier\Category\Category;
use Lighthouse\CoreBundle\Document\Classifier\SubCategory\SubCategory;
use Lighthouse\CoreBundle\Document\Classifier\SubCategory\SubCategoryRepository;
use Lighthouse\CoreBundle\Document\Store\Store;
use Lighthouse\CoreBundle\Exception\FlushFailedException;
use Lighthouse\CoreBundle\Form\Classifier\CatalogGroupType;
use Lighthouse\CoreBundle\Form\Classifier\SubCategoryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use JMS\DiExtraBundle\Annotation as DI;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use MongoDuplicateKeyException;
use Exception;

class SubCategoryController extends AbstractRestController
{
    /**
     * @DI\Inject("lighthouse.core.document.repository.classifier.subcategory")
     * @var SubCategoryRepository
     */
    protected $documentRepository;

    /**
     * @DI\Inject("lighthouse.core.document.catalog.manager")
     * @var CatalogManager
     */
    protected $catalogManager;

    /**
     * @return AbstractType
     */
    protected function getDocumentFormType()
    {
        return new SubCategoryType();
    }

    /**
     * @param FlushFailedException $e
     * @return FormInterface
     */
    protected function handleFlushFailedException(FlushFailedException $e)
    {
        if ($e->getCause() instanceof MongoDuplicateKeyException) {
            return $this->addFormError($e->getForm(), 'name', 'lighthouse.validation.errors.subCategory.name.unique');
        } else {
            return parent::handleFlushFailedException($e);
        }
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws FlushFailedException
     * @return FormInterface|SubCategory
     * @Rest\View(statusCode=201)
     * @Secure(roles="ROLE_COMMERCIAL_MANAGER")
     * @ApiDoc(resource=true)
     */
    public function postSubcategoriesAction(Request $request)
    {
        return $this->processPost($request);
    }

    /**
     * @param Request $request
     * @param SubCategory $subCategory
     * @return FormInterface|SubCategory
     * @Secure(roles="ROLE_COMMERCIAL_MANAGER")
     * @ApiDoc
     */
    public function putSubcategoriesAction(Request $request, SubCategory $subCategory)
    {
        return $this->processForm($request, $subCategory);
    }

    /**
     * @param SubCategory $subCategory
     * @return SubCategory
     * @Secure(roles="ROLE_COMMERCIAL_MANAGER")
     * @ApiDoc
     */
    public function getSubcategoryAction(SubCategory $subCategory)
    {
        return $subCategory;
    }

    /**
     * @param Store $store
     * @param SubCategory $subCategory
     * @return SubCategory
     * @SecureParam(name="store", permissions="ACL_STORE_MANAGER,ACL_DEPARTMENT_MANAGER")
     * @ApiDoc(
     *      resource = true
     * )
     */
    public function getStoreSubcategoryAction(Store $store, SubCategory $subCategory)
    {
        return $subCategory;
    }

    /**
     * @param Category $category
     * @return SubCategory[]|Cursor
     * @Secure(roles="ROLE_COMMERCIAL_MANAGER")
     * @ApiDoc(
     *      resource = true
     * )
     */
    public function getCategorySubcategoriesAction(Category $category)
    {
        return $this->documentRepository->findByParent($category->id);
    }

    /**
     * @param Store $store
     * @param Category $category
     * @return SubCategory[]|Cursor
     * @SecureParam(name="store", permissions="ACL_STORE_MANAGER,ACL_DEPARTMENT_MANAGER")
     * @ApiDoc
     */
    public function getStoreCategorySubcategoriesAction(Store $store, Category $category)
    {
        return $this->documentRepository->findByParent($category->id);
    }

    /**
     * @param SubCategory $subCategory
     * @return void
     * @Secure(roles="ROLE_COMMERCIAL_MANAGER")
     * @ApiDoc
     */
    public function deleteSubcategoriesAction(SubCategory $subCategory)
    {
        $this->processDelete($subCategory);
    }

    /**
     * @param Request $request
     * @return Form|SubCategory
     *
     * @Rest\View(statusCode=201)
     * @Rest\Route("catalog/groups")
     * @Secure(roles="ROLE_COMMERCIAL_MANAGER")
     * @ApiDoc
     */
    public function postCatalogGroupsAction(Request $request)
    {
        $catalogGroup = $this->catalogManager->createNewCatalogGroup();

        $formType = new CatalogGroupType();
        $form = $this->createForm($formType, $catalogGroup);

        $form->handleRequest($request);

        if ($form->isValid()) {
            return $this->saveDocument($catalogGroup, $form);
        } else {
            return $form;
        }
    }

    /**
     * @param SubCategory $catalogGroup
     * @return SubCategory
     *
     * @Rest\Route("catalog/groups/{catalogGroup}")
     * @Secure(roles="ROLE_COMMERCIAL_MANAGER")
     * @ApiDoc
     */
    public function getCatalogGroupAction(SubCategory $catalogGroup)
    {
        return $catalogGroup;
    }

    /**
     * @return Cursor|SubCategory[]
     *
     * @Rest\Route("catalog/groups")
     * @Secure(roles="ROLE_COMMERCIAL_MANAGER")
     * @ApiDoc
     */
    public function getCatalogGroupsAction()
    {
        $defaultCategory = $this->catalogManager->getDefaultCategory();
        return $this->documentRepository->findByParent($defaultCategory->id);
    }
}
