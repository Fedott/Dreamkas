<?php

namespace Lighthouse\CoreBundle\Document\Category;

use Lighthouse\CoreBundle\Document\AbstractDocument;
use Lighthouse\CoreBundle\Document\Group\Group;
use Lighthouse\CoreBundle\Document\SubCategory\SubCategory;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique;

/**
 * @MongoDB\Document(
 *     repositoryClass="Lighthouse\CoreBundle\Document\Category\CategoryRepository"
 * )
 * @Unique(fields={"name", "group"}, message="lighthouse.validation.errors.category.name.unique")
 */
class Category extends AbstractDocument
{
    /**
     * @MongoDB\Id
     * @var string
     */
    protected $id;

    /**
     * Наименование
     * @MongoDB\String
     * @Assert\NotBlank
     * @Assert\Length(max="100", maxMessage="lighthouse.validation.errors.length")
     * @var string
     */
    protected $name;

    /**
     * @MongoDB\ReferenceOne(
     *     targetDocument="Lighthouse\CoreBundle\Document\Group\Group",
     *     simple=true,
     *     cascade="persist"
     * )
     * @var Group
     */
    protected $group;

    /**
     * @MongoDB\ReferenceMany(
     *      targetDocument="Lighthouse\CoreBundle\Document\SubCategory\SubCategory",
     *      simple=true,
     *      cascade="persist",
     *      mappedBy="category"
     * )
     * @var SubCategory[]
     */
    protected $subCategories;
}
