<?php

namespace Lighthouse\CoreBundle\Document\Report\GrossSales\Classifier;

use Lighthouse\CoreBundle\Document\AbstractCollection;
use Lighthouse\CoreBundle\Document\Classifier\AbstractNode;
use DateTime;
use Lighthouse\CoreBundle\Document\Report\GrossSales\TodayGrossSales;

abstract class GrossSalesByClassifierNodeCollection extends AbstractCollection
{
    /**
     * @param AbstractNode $node
     * @param array $dates
     * @return TodayGrossSales
     */
    abstract public function createReport(AbstractNode $node, array $dates);

    /**
     * @param AbstractNode $node
     * @param DateTime[] $dates
     * @return TodayGrossSales
     */
    public function createByNode(AbstractNode $node, array $dates)
    {
        $report = $this->createReport($node, $dates);
        $this->set($node->id, $report);
        return $report;
    }

    /**
     * @param AbstractNode $node
     * @param array $dates
     * @return TodayGrossSales
     */
    public function getByNode(AbstractNode $node, array $dates)
    {
        if ($this->containsKey($node->id)) {
            return $this->get($node->id);
        } else {
            return $this->createByNode($node, $dates);
        }
    }
}
