<?php

namespace Lighthouse\ReportsBundle\Document\GrossSales\Classifier\Group;

use Lighthouse\CoreBundle\Document\Classifier\AbstractNode;
use Lighthouse\CoreBundle\Document\Classifier\Group\Group;
use Lighthouse\ReportsBundle\Document\GrossSales\Classifier\GrossSalesNodeReport;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @property Group $group
 *
 * @MongoDB\Document(
 *      repositoryClass=
 *      "Lighthouse\ReportsBundle\Document\GrossSales\Classifier\Group\GrossSalesGroupRepository"
 * )
 * @MongoDB\Index(keys={"dayHour"="asc", "group"="asc", "store"="asc"})
 */
class GrossSalesGroupReport extends GrossSalesNodeReport
{
    /**
     * @MongoDB\ReferenceOne(
     *     targetDocument="Lighthouse\CoreBundle\Document\Classifier\Group\Group",
     *     simple=true
     * )
     * @var Group
     */
    protected $group;

    /**
     * @return AbstractNode|Group
     */
    public function getNode()
    {
        return $this->group;
    }

    /**
     * @param AbstractNode $node
     */
    public function setNode(AbstractNode $node)
    {
        $this->group = $node;
    }
}
