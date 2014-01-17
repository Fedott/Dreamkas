<?php

namespace Lighthouse\CoreBundle\Document\Report\GrossSales;

use Lighthouse\CoreBundle\Document\AbstractCollection;

class DayHoursGrossSalesCollection extends AbstractCollection
{
    /**
     * Sort by keys
     * @return $this
     */
    public function keySort()
    {
        $values = $this->toArray();
        ksort($values);
        $this->setValues($values);
        return $this;
    }
}