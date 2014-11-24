<?php

namespace Lighthouse\CoreBundle\Document\Store;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation\SoftDeleteable;
use Lighthouse\CoreBundle\Document\AbstractDocument;
use Lighthouse\CoreBundle\Document\Department\Department;
use Lighthouse\CoreBundle\Document\SoftDeleteableDocument;
use Lighthouse\CoreBundle\Document\User\User;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique;
use InvalidArgumentException;
use JMS\Serializer\Annotation\Exclude;
use DateTime;

/**
 * @property string $id
 * @property string $name
 * @property string $address
 * @property string $contacts
 * @property ArrayCollection|Department[] $departments
 * @property Collection|User[] $storeManagers
 * @property Collection|User[] $departmentManagers
 *
 * @MongoDB\Document(repositoryClass="Lighthouse\CoreBundle\Document\Store\StoreRepository")
 * @Unique(fields="name", message="lighthouse.validation.errors.store.name.unique")
 *
 * @SoftDeleteable
 */
class Store extends AbstractDocument implements SoftDeleteableDocument
{
    const REL_STORE_MANAGERS = 'storeManagers';
    const REL_DEPARTMENT_MANAGERS = 'departmentManagers';

    /**
     * @Exclude
     * @var array
     */
    public static $roles = array(
        self::REL_DEPARTMENT_MANAGERS => User::ROLE_DEPARTMENT_MANAGER,
        self::REL_STORE_MANAGERS => User::ROLE_STORE_MANAGER
    );

    /**
     * @MongoDB\Id
     * @var string
     */
    protected $id;

    /**
     * Номер
     * @MongoDB\String
     * @MongoDB\UniqueIndex
     * @Assert\NotBlank
     * @Assert\Length(max="50", maxMessage="lighthouse.validation.errors.length")
     * @var string
     */
    protected $name;

    /**
     * Адрес
     * @MongoDB\String
     * @Assert\Length(max="300", maxMessage="lighthouse.validation.errors.length")
     * @var string
     */
    protected $address;

    /**
     * Контакты
     * @MongoDB\String
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
     * @var Department[]|ArrayCollection
     */
    protected $departments;

    /**
     * @MongoDB\ReferenceMany(
     *      targetDocument="Lighthouse\CoreBundle\Document\User\User",
     *      simple=true,
     *      cascade="persist"
     * )
     * @var User[]|Collection
     */
    protected $storeManagers;

    /**
     * @MongoDB\ReferenceMany(
     *      targetDocument="Lighthouse\CoreBundle\Document\User\User",
     *      simple=true,
     *      cascade="persist"
     * )
     * @var User[]|Collection
     */
    protected $departmentManagers;

    /**
     * @MongoDB\Date
     * @var DateTime
     */
    protected $deletedAt;

    /**
     *
     */
    public function __construct()
    {
        $this->departments = new ArrayCollection();
        $this->storeManagers = new ArrayCollection();
        $this->departmentManagers = new ArrayCollection();
    }

    /**
     * @return DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @return string|null
     */
    public function getSoftDeleteableName()
    {
        return 'name';
    }

    /**
     * @param string $rel
     * @return Collection|User[]
     * @throws InvalidArgumentException
     */
    public function getManagersByRel($rel)
    {
        switch ($rel) {
            case self::REL_STORE_MANAGERS:
                return $this->storeManagers;
            case self::REL_DEPARTMENT_MANAGERS:
                return $this->departmentManagers;
            default:
                throw new InvalidArgumentException(sprintf("Invalid rel '%s' given", $rel));
        }
    }

    /**
     * @param string $rel
     * @throws InvalidArgumentException
     * @return string
     */
    public static function getRoleByRel($rel)
    {
        if (isset(self::$roles[$rel])) {
            return self::$roles[$rel];
        }
        throw new InvalidArgumentException(sprintf("Invalid rel '%s' given", $rel));
    }
}
