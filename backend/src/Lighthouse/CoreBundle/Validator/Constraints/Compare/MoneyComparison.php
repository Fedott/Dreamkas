<?php

namespace Lighthouse\CoreBundle\Validator\Constraints\Compare;

use Lighthouse\CoreBundle\Exception\NullValueException;
use Lighthouse\CoreBundle\Types\Money;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class MoneyComparison extends Comparison
{
    /**
     * @var Money
     */
    protected $moneyValue;

    /**
     * @param Money|mixed $value
     * @param Comparator $comparator
     * @throws UnexpectedTypeException
     */
    public function __construct($value, Comparator $comparator = null)
    {
        $this->moneyValue = $value;
        parent::__construct($value, $comparator);
    }

    /**
     * @param mixed $value
     * @throws UnexpectedTypeException
     * @throws NullValueException
     * @return int
     */
    protected function normalizeValue($value)
    {
        if (null === $value) {
            throw new NullValueException('money');
        } elseif (!$value instanceof Money) {
            throw new UnexpectedTypeException($value, 'Money');
        } elseif ($value->isNull()) {
            throw new NullValueException('money');
        }
        return parent::normalizeValue($value->getCount());
    }

    /**
     * @return Money
     */
    public function getMoneyValue()
    {
        return $this->moneyValue;
    }
}
