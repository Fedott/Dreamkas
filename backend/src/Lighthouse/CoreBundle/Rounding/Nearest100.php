<?php

namespace Lighthouse\CoreBundle\Rounding;

use JMS\DiExtraBundle\Annotation as DI;
use Lighthouse\CoreBundle\Types\Numeric\Money;

/**
 * @DI\Service("lighthouse.core.rounding.nearest100")
 * @DI\Tag("rounding")
 */
class Nearest100 extends AbstractRounding
{
    const NAME = 'nearest100';
    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @param Money $value
     * @return Money
     */
    public function round(Money $value)
    {
        $rounded = round($value->getCount(), -2);
        return new Money($rounded);
    }
}
