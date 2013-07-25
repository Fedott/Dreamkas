<?php

namespace Lighthouse\CoreBundle\Document\Classifier\Group;

use Lighthouse\CoreBundle\Document\Classifier\AbstractNode;
use Lighthouse\CoreBundle\Document\Classifier\Category\Category;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique;

/**
 * @MongoDB\Document(
 *     repositoryClass="Lighthouse\CoreBundle\Document\Classifier\Group\GroupRepository"
 * )
 * @Unique(fields="name", message="lighthouse.validation.errors.group.name.unique")
 */
class Group extends AbstractNode
{
    /**
     * @MongoDB\ReferenceMany(
     *      targetDocument="Lighthouse\CoreBundle\Document\Classifier\Category\Category",
     *      simple=true,
     *      cascade="persist",
     *      mappedBy="group"
     * )
     * @var \Lighthouse\CoreBundle\Document\Classifier\Category\Category[]
     */
    protected $categories;
}
