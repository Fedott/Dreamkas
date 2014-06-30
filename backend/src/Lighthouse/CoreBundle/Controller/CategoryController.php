<?php

namespace Lighthouse\CoreBundle\Controller;

use Doctrine\ODM\MongoDB\Cursor;
use Lighthouse\CoreBundle\Document\Classifier\Category\Category;
use Lighthouse\CoreBundle\Document\Classifier\Category\CategoryRepository;
use Lighthouse\CoreBundle\Document\Store\Store;
use Lighthouse\CoreBundle\Exception\FlushFailedException;
use Lighthouse\CoreBundle\Form\CategoryType;
use Lighthouse\CoreBundle\Document\Classifier\Group\Group;
use JMS\DiExtraBundle\Annotation as DI;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use MongoDuplicateKeyException;
use Exception;

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
     * @param FlushFailedException $e
     * @return FormInterface
     */
    protected function handleFlushFailedException(FlushFailedException $e)
    {
        if ($e->getCause() instanceof MongoDuplicateKeyException) {
            return $this->addFormError($e->getForm(), 'name', 'lighthouse.validation.errors.category.name.unique');
        } else {
            return parent::handleFlushFailedException($e);
        }
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws FlushFailedException
     * @return FormInterface|Category
     * @Rest\View(statusCode=201)
     * @Secure(roles="ROLE_COMMERCIAL_MANAGER")
     * @ApiDoc(resource=true)
     */
    public function postCategoriesAction(Request $request)
    {
        return $this->processPost($request);
    }

    /**
     * @param Request $request
     * @param Category $category
     * @return FormInterface|Category
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
     * @param Group $group
     * @return Category[]|Cursor
     * @Secure(roles="ROLE_COMMERCIAL_MANAGER")
     * @ApiDoc
     */
    public function getGroupCategoriesAction(Group $group)
    {
        return $this->documentRepository->findByParent($group->id);
    }


    /**
     * @param Store $store
     * @param Group $group
     * @return Category[]|Cursor
     * @SecureParam(name="store", permissions="ACL_STORE_MANAGER,ACL_DEPARTMENT_MANAGER")
     * @ApiDoc
     */
    public function getStoreGroupCategoriesAction(Store $store, Group $group)
    {
        return $this->documentRepository->findByParent($group->id);
    }

    /**
     * @param Category $category
     * @return void
     * @Secure(roles="ROLE_COMMERCIAL_MANAGER")
     * @ApiDoc
     */
    public function deleteCategoriesAction(Category $category)
    {
        $this->processDelete($category);
    }
}
