<?php

namespace Lighthouse\CoreBundle\Controller;

use Lighthouse\CoreBundle\Document\Classifier\Group\Group;
use Lighthouse\CoreBundle\Document\Classifier\Group\GroupCollection;
use Lighthouse\CoreBundle\Document\Classifier\Group\GroupRepository;
use Lighthouse\CoreBundle\Form\GroupType;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class GroupController extends AbstractRestController
{
    /**
     * @DI\Inject("lighthouse.core.document.repository.classifier.group")
     * @var \Lighthouse\CoreBundle\Document\Classifier\Group\GroupRepository
     */
    protected $documentRepository;

    /**
     * @return GroupType
     */
    protected function getDocumentFormType()
    {
        return new GroupType();
    }

    /**
     * @Rest\View(statusCode=201)
     *
     * @param Request $request
     * @return \FOS\RestBundle\View\View|\Lighthouse\CoreBundle\Document\Classifier\Group\Group
     * @Secure(roles="ROLE_COMMERCIAL_MANAGER")
     * @ApiDoc
     */
    public function postGroupsAction(Request $request)
    {
        return $this->processPost($request);
    }

    /**
     * @Rest\View(statusCode=200)
     *
     * @param Request $request
     * @param \Lighthouse\CoreBundle\Document\Classifier\Group\Group $group
     * @return \FOS\RestBundle\View\View|\Lighthouse\CoreBundle\Document\Classifier\Group\Group
     * @Secure(roles="ROLE_COMMERCIAL_MANAGER")
     * @ApiDoc
     */
    public function putGroupsAction(Request $request, Group $group)
    {
        return $this->processForm($request, $group);
    }

    /**
     * @param \Lighthouse\CoreBundle\Document\Classifier\Group\Group $group
     * @return null
     * @Secure(roles="ROLE_COMMERCIAL_MANAGER")
     * @ApiDoc
     */
    public function deleteGroupsAction(Group $group)
    {
        return $this->processDelete($group);
    }

    /**
     * @param \Lighthouse\CoreBundle\Document\Classifier\Group\Group $group
     * @return Group
     * @Secure(roles="ROLE_COMMERCIAL_MANAGER,ROLE_DEPARTMENT_MANAGER,ROLE_STORE_MANAGER")
     * @ApiDoc
     */
    public function getGroupAction(Group $group)
    {
        return $group;
    }

    /**
     * @return \Lighthouse\CoreBundle\Document\Classifier\Group\GroupCollection
     * @Secure(roles="ROLE_COMMERCIAL_MANAGER,ROLE_DEPARTMENT_MANAGER,ROLE_STORE_MANAGER")
     * @ApiDoc(
     *      resource=true
     * )
     */
    public function getGroupsAction()
    {
        $cursor = $this->documentRepository->findAll();
        $collection = new GroupCollection($cursor);
        return $collection;
    }
}
