<?php

namespace Lighthouse\CoreBundle\Integration\Set10\ImportSales;

use Lighthouse\CoreBundle\Integration\Set10\SimpleXMLElement;
use DateTime;

class PurchaseElement extends SimpleXMLElement
{
    /**
     * @return string
     */
    public function getSaleTime()
    {
        return (string) $this['saletime'];

    }

    /**
     * @return DateTime
     */
    public function getSaleDateTime()
    {
        return DateTime::createFromFormat('Y-m-d\TH:i:s.uP', $this->getSaleTime());
    }

    /**
     * @return string
     */
    public function getShop()
    {
        return (string) $this['shop'];
    }

    /**
     * @return bool
     */
    public function getOperationType()
    {
        switch ((string) $this['operationType']) {
            case 'true':
                return true;
            case 'false':
                return false;
        }
    }

    /**
     * @return PositionElement[]
     */
    public function getPositions()
    {
        $positions = array();
        foreach ($this->positions->position as $position) {
            $positions[] = new PositionElement($position);
        }
        return $positions;
    }
}
