<?php

namespace Lighthouse\CoreBundle\Document\Classifier\SubCategory;

use Lighthouse\CoreBundle\Document\Classifier\AbstractNode;
use Lighthouse\CoreBundle\Document\Classifier\Category\Category;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique;

/**
 * @property Category $category
 *
 * @MongoDB\Document(
 *     repositoryClass="Lighthouse\CoreBundle\Document\Classifier\SubCategory\SubCategoryRepository"
 * )
 * @Unique(fields={"name", "category"}, message="lighthouse.validation.errors.subCategory.name.unique")
 */
class SubCategory extends AbstractNode
{
    /**
     * @MongoDB\ReferenceOne(
     *     targetDocument="Lighthouse\CoreBundle\Document\Classifier\Category\Category",
     *     simple=true,
     *     cascade="persist",
     *     inversedBy="subCategories"
     * )
     * @var Category
     */
    protected $category;

    /**
     * @return Category
     */
    public function getParent()
    {
        return $this->category;
    }
}
