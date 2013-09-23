<?php

namespace Lighthouse\CoreBundle\Controller;

use Lighthouse\CoreBundle\Document\Store\Store;
use Lighthouse\CoreBundle\Document\WriteOff\WriteOff;
use Lighthouse\CoreBundle\Document\WriteOff\WriteOffCollection;
use Lighthouse\CoreBundle\Document\WriteOff\WriteOffRepository;
use Lighthouse\CoreBundle\Form\WriteOffType;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WriteOffController extends AbstractRestController
{
    /**
     * @DI\Inject("lighthouse.core.document.repository.writeoff")
     * @var WriteOffRepository
     */
    protected $documentRepository;

    /**
     * @return WriteOffType|\Symfony\Component\Form\AbstractType
     */
    protected function getDocumentFormType()
    {
        return new WriteOffType();
    }

    /**
     * @Rest\View(statusCode=201)
     *
     * @param Store $store
     * @param Request $request
     * @return \FOS\RestBundle\View\View|WriteOff
     * @Secure(roles="ROLE_DEPARTMENT_MANAGER")
     * @SecureParam(name="store", permissions="ACL_DEPARTMENT_MANAGER")
     * @ApiDoc
     */
    public function postWriteoffsAction(Store $store, Request $request)
    {
        $writeOff = new WriteOff;
        $writeOff->store = $store;
        return $this->processForm($request, $writeOff);
    }

    /**
     * @Rest\View(statusCode=200)
     *
     * @param Store $store
     * @param WriteOff $writeOff
     * @param Request $request
     * @return \FOS\RestBundle\View\View|WriteOff
     * @Secure(roles="ROLE_DEPARTMENT_MANAGER")
     * @SecureParam(name="store", permissions="ACL_DEPARTMENT_MANAGER")
     * @ApiDoc
     */
    public function putWriteoffsAction(Store $store, WriteOff $writeOff, Request $request)
    {
        $this->checkWriteoffStore($store, $writeOff);
        return $this->processForm($request, $writeOff);
    }

    /**
     * @param Store $store
     * @param WriteOff $writeOff
     * @return \Lighthouse\CoreBundle\Document\WriteOff\WriteOff
     * @Secure(roles="ROLE_DEPARTMENT_MANAGER")
     * @SecureParam(name="store", permissions="ACL_DEPARTMENT_MANAGER")
     * @ApiDoc
     */
    public function getWriteoffAction(Store $store, WriteOff $writeOff)
    {
        $this->checkWriteoffStore($store, $writeOff);
        return $writeOff;
    }

    /**
     * @param Store $store
     * @return WriteOffCollection
     * @Secure(roles="ROLE_DEPARTMENT_MANAGER")
     * @SecureParam(name="store", permissions="ACL_DEPARTMENT_MANAGER")
     * @ApiDoc(
     *      resource=true
     * )
     */
    public function getWriteoffsAction(Store $store)
    {
        $cursor = $this->documentRepository->findByStore($store->id);
        $collection = new WriteOffCollection($cursor);
        return $collection;
    }

    /**
     * @param Store $store
     * @param WriteOff $writeoff
     * @throws NotFoundHttpException
     */
    protected function checkWriteoffStore(Store $store, WriteOff $writeoff)
    {
        if ($writeoff->store !== $store) {
            throw new NotFoundHttpException(sprintf("%s object not found", get_class($writeoff)));
        }
    }
}
