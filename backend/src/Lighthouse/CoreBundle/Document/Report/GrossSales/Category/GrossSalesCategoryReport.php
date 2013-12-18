<?php

namespace Lighthouse\CoreBundle\Document\Report\GrossSales\Category;

use Lighthouse\CoreBundle\Document\Classifier\AbstractNode;
use Lighthouse\CoreBundle\Document\Classifier\Category\Category;
use Lighthouse\CoreBundle\Document\Report\GrossSales\GrossSalesClassifierNodeReport;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @property Category $category
 *
 * @MongoDB\Document(
 *      repositoryClass="Lighthouse\CoreBundle\Document\Report\GrossSales\Category\GrossSalesCategoryRepository"
 * )
 */
class GrossSalesCategoryReport extends GrossSalesClassifierNodeReport
{
    /**
     * @MongoDB\ReferenceOne(
     *     targetDocument="Lighthouse\CoreBundle\Document\Classifier\Category\Category",
     *     simple=true
     * )
     * @var Category
     */
    protected $category;

    /**
     * @return AbstractNode|Category
     */
    public function getNode()
    {
        return $this->category;
    }
}
