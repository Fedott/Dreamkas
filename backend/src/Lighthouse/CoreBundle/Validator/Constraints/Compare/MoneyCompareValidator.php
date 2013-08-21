<?php

namespace Lighthouse\CoreBundle\Validator\Constraints\Compare;

use Lighthouse\CoreBundle\Types\Money;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class MoneyCompareValidator extends CompareValidator
{
    /**
     * @param Money $value
     * @return int
     * @throws \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    protected function normalizeFieldValue($value)
    {
        if (!$value instanceof Money) {
            throw new UnexpectedTypeException($value, 'Money');
        }

        return $value->getCount();
    }

    /**
     * @param Money $value
     * @param Constraint|MoneyCompare $constraint
     * @return mixed
     */
    protected function formatMessageValue($value, Constraint $constraint)
    {
        $digits = (int) $constraint->digits;
        $divider = pow(10, $digits);

        return $value->getCount() / $divider;
    }
}
