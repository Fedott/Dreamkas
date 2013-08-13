<?php

namespace Lighthouse\CoreBundle\Rounding;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("lighthouse.core.rounding.manager")
 */
class RoundingManager
{
    /**
     * @var AbstractRounding[]
     */
    protected $roundings = array();

    /**
     * @param AbstractRounding $rounding
     */
    public function add(AbstractRounding $rounding)
    {
        $this->roundings[] = $rounding;
    }

    /**
     * @param string $name
     * @return AbstractRounding
     */
    public function findByName($name)
    {
        foreach ($this->roundings as $rounding) {
            if ($name == $rounding->getName()) {
                return $rounding;
            }
        }
    }

    /**
     * @return array|AbstractRounding[]
     */
    public function findAll()
    {
        return $this->roundings;
    }
}
