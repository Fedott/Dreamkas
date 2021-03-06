<?php

namespace Lighthouse\CoreBundle\Document\Supplier;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Gedmo\Mapping\Annotation\SoftDeleteable;
use Lighthouse\CoreBundle\Document\AbstractDocument;
use Lighthouse\CoreBundle\Document\BankAccount\BankAccount;
use Lighthouse\CoreBundle\Document\File\File;
use Lighthouse\CoreBundle\Document\LegalDetails\LegalDetails;
use Lighthouse\CoreBundle\Document\Organization\Organizationable;
use Lighthouse\CoreBundle\Document\SoftDeleteableDocument;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property string $id
 * @property string $name
 * @property string $address
 * @property string $phone
 * @property string $fax
 * @property string $email
 * @property string $contactPerson
 * @property File $agreement
 * @property LegalDetails $legalDetails
 * @property BankAccount[]|Collection $bankAccounts
 *
 * @MongoDB\Document(
 *      repositoryClass="Lighthouse\CoreBundle\Document\Supplier\SupplierRepository"
 * )
 * @Unique(fields="name", message="lighthouse.validation.errors.supplier.name.unique")
 *
 * @SoftDeleteable
 */
class Supplier extends AbstractDocument implements Organizationable, SoftDeleteableDocument
{
    const TYPE = 'Supplier';

    /**
     * @MongoDB\Id
     * @var string
     */
    protected $id;

    /**
     * @MongoDB\String
     * @MongoDB\UniqueIndex
     * @Assert\NotBlank
     * @Assert\Length(max="100", maxMessage="lighthouse.validation.errors.length")
     * @var string
     */
    protected $name;

    /**
     * @MongoDB\String
     * @Assert\Length(max=300, maxMessage="lighthouse.validation.errors.length")
     * @var string
     */
    protected $address;

    /**
     * @MongoDB\String
     * @Assert\Length(max="300", maxMessage="lighthouse.validation.errors.length")
     * @var string
     */
    protected $phone;

    /**
     * @MongoDB\String
     * @Assert\Length(max="300", maxMessage="lighthouse.validation.errors.length")
     * @var string
     */
    protected $fax;

    /**
     * @MongoDB\String
     * @Assert\Length(max="300", maxMessage="lighthouse.validation.errors.length")
     * @var string
     */
    protected $email;

    /**
     * @MongoDB\String
     * @Assert\Length(max="300", maxMessage="lighthouse.validation.errors.length")
     * @var string
     */
    protected $contactPerson;

    /**
     * @MongoDB\ReferenceOne(
     *     targetDocument="Lighthouse\CoreBundle\Document\File\File",
     *     simple=true
     * )
     * @var File
     */
    protected $agreement;

    /**
     * @MongoDB\EmbedOne(
     *   targetDocument="Lighthouse\CoreBundle\Document\LegalDetails\LegalDetails"
     * )
     * @var LegalDetails
     */
    protected $legalDetails;

    /**
     * @MongoDB\ReferenceMany(
     *      targetDocument="Lighthouse\CoreBundle\Document\BankAccount\BankAccount",
     *      simple=true,
     *      cascade="persist",
     *      mappedBy="organization"
     * )
     * @var BankAccount[]|Collection
     */
    protected $bankAccounts;

    /**
     * @MongoDB\Date
     * @var DateTime
     */
    protected $deletedAt;

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
     * @return LegalDetails
     */
    public function getLegalDetails()
    {
        return $this->legalDetails;
    }

    /**
     * @param LegalDetails $legalDetails
     * @return $this
     */
    public function setLegalDetails(LegalDetails $legalDetails)
    {
        $this->legalDetails = $legalDetails;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrganizationType()
    {
        return self::TYPE;
    }

    /**
     * @return string
     */
    public function getOrganizationId()
    {
        return $this->id;
    }
}
