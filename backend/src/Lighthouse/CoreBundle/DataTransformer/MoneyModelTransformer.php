<?php

namespace Lighthouse\CoreBundle\DataTransformer;

use Lighthouse\CoreBundle\Types\Money;
use Symfony\Component\Form\DataTransformerInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @DI\Service("lighthouse.core.data_transformer.money_model")
 */
class MoneyModelTransformer implements DataTransformerInterface
{
    /**
     * @var int
     */
    protected $divider;

    /**
     * @var int
     */
    protected $digits = 2;

    /**
     * @param int $divider
     */
    public function __construct($digits = null)
    {
        if (null !== $digits) {
            $this->digits = (int) $digits;
        }
        $this->divider = pow(10, $this->digits);
    }

    /**
     * @param Money $value
     * @return int
     */
    public function transform($value)
    {
        if (null === $value) {
            $value = 0;
        } else if ($value instanceof Money) {
            $value = $value->getCount();
        } else {
            throw new TransformationFailedException('Value should be Money type object or null. ' . gettype($value) . ' given');
        }
        $value /= $this->divider;
        return $value;
    }

    /**
     * @param int $value
     * @return Money
     */
    public function reverseTransform($value)
    {
        $value *= $this->divider;
        return new Money($value);
    }
}
