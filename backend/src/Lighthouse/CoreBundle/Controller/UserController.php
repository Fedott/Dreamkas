<?php

namespace Lighthouse\CoreBundle\Controller;

use Doctrine\ODM\MongoDB\LoggableCursor;
use FOS\Rest\Util\Codes;
use Lighthouse\CoreBundle\Document\User\User;
use Lighthouse\CoreBundle\Document\User\UserCollection;
use Lighthouse\CoreBundle\Document\User\UserRepository;
use Lighthouse\CoreBundle\Form\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

class UserController extends AbstractRestController
{
    /**
     * @DI\Inject("lighthouse.core.document.repository.user")
     * @var UserRepository
     */
    protected $documentRepository;

    /**
     * @DI\Inject("security.encoder_factory")
     * @var EncoderFactory
     */
    public $encodeFactory;

    /**
     * @return AbstractType
     */
    protected function getDocumentFormType()
    {
        return new UserType();
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \FOS\RestBundle\View\View|\Lighthouse\CoreBundle\Document\Invoice\Invoice
     *
     * @Rest\View(statusCode=201)
     */
    public function postUsersAction(Request $request)
    {
        /** @var User $document */
        $document = $this->getDocumentRepository()->createNew();

        $type = $this->getDocumentFormType();
        $form = $this->createForm($type, $document);
        $form->submit($request);

        if ($form->isValid()) {
            // Set encode password
            $encoder = $this->encodeFactory->getEncoder($document);
            $document->salt = md5(date('cr'));
            $document->password = $encoder->encodePassword($document->password, $document->getSalt());

            $this->getDocumentRepository()->getDocumentManager()->persist($document);
            $this->getDocumentRepository()->getDocumentManager()->flush();
            return $document;
        } else {
            return $this->view($form, Codes::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return User
     */
    public function getUserAction($id)
    {
        return $this->processGet($id);
    }

    /**
     * @return \Lighthouse\CoreBundle\Document\User\UserCollection
     */
    public function getUsersAction()
    {
        /* @var LoggableCursor $cursor */
        $cursor = $this->getDocumentRepository()->findAll();
        $collection = new UserCollection($cursor);
        return $collection;
    }
}
