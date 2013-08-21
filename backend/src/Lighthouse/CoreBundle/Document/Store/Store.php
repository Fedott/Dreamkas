<?php

namespace Lighthouse\CoreBundle\Document\Store;

use Doctrine\Common\Collections\ArrayCollection;
use Lighthouse\CoreBundle\Document\AbstractDocument;
use Lighthouse\CoreBundle\Document\Department\Department;
use Lighthouse\CoreBundle\Document\User\User;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique;

/**
 * @property string $id
 * @property string $number
 * @property string $address
 * @property string $contacts
 * @property ArrayCollection|Department[] $departments
 * @property ArrayCollection|User[] $managers
 *
 * @MongoDB\Document(
 *     repositoryClass="Lighthouse\CoreBundle\Document\Store\StoreRepository"
 * )
 * @Unique(fields="number", message="lighthouse.validation.errors.store.number.unique")
 */
class Store extends AbstractDocument
{
    /**
     * @MongoDB\Id
     * @var string
     */
    protected $id;

    /**
     * Номер
     * @MongoDB\String
     * @Assert\NotBlank
     * @Assert\Regex("/^[\w\d_\-]+$/u")
     * @Assert\Length(max="50", maxMessage="lighthouse.validation.errors.length")
     * @var string
     */
    protected $number;

    /**
     * Адрес
     * @MongoDB\String
     * @Assert\NotBlank
     * @Assert\Length(max="300", maxMessage="lighthouse.validation.errors.length")
     * @var string
     */
    protected $address;

    /**
     * Контакты
     * @MongoDB\String
     * @Assert\NotBlank
     * @Assert\Length(max="100", maxMessage="lighthouse.validation.errors.length")
     * @var string
     */
    protected $contacts;

    /**
     * @MongoDB\ReferenceMany(
     *      targetDocument="Lighthouse\CoreBundle\Document\Department\Department",
     *      simple=true,
     *      cascade="persist",
     *      mappedBy="store"
     * )
     * @var Department[]
     */
    protected $departments;

    /**
     * @MongoDB\ReferenceMany(
     *      targetDocument="Lighthouse\CoreBundle\Document\User\User",
     *      simple=true,
     *      cascade="persist"
     * )
     * @var User[]
     */
    protected $managers;

    /**
     *
     */
    public function __construct()
    {
        $this->managers = new ArrayCollection();
    }
}
