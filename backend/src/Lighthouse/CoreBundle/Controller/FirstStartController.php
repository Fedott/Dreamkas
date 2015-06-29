<?php

namespace Lighthouse\CoreBundle\Controller;

use Lighthouse\CoreBundle\Document\FirstStart\FirstStart;
use Lighthouse\CoreBundle\Document\FirstStart\FirstStartRepository;
use Lighthouse\CoreBundle\Form\FirstStartType;
use Symfony\Component\Form\FormTypeInterface;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FirstStartController extends AbstractRestController
{
    /**
     * @DI\Inject("lighthouse.core.document.repository.first_start")
     * @var FirstStartRepository
     */
    protected $documentRepository;

    /**
     * @return FirstStartType
     */
    protected function getDocumentFormType()
    {
        return new FirstStartType();
    }

    /**
     * @return FirstStart
     * @Rest\Route("firstStart")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Secure(roles="ROLE_COMMERCIAL_MANAGER")
     * @ApiDoc(resource=true)
     */
    public function getFirstStartAction()
    {
        $firstStart = $this->documentRepository->findOrCreate();
        $this->documentRepository->populateFirstStart($firstStart);
        return $firstStart;
    }

    /**
     * @param Request $request
     * @return FirstStart|FormInterface
     * @Rest\Route("firstStart")
     * @Secure(roles="ROLE_COMMERCIAL_MANAGER")
     * @ApiDoc
     */
    public function putFirstStartAction(Request $request)
    {
        $repository = $this->documentRepository;
        return $this->processFormCallback(
            $request,
            function (FirstStart $firstStart) use ($repository) {
                $repository->save($firstStart);
                return $repository->populateFirstStart($firstStart);
            },
            $repository->findOrCreate()
        );
    }
}
