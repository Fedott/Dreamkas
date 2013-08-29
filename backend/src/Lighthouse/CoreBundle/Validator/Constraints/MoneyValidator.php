<?php

namespace Lighthouse\CoreBundle\Validator\Constraints;

use Lighthouse\CoreBundle\Types\Money as MoneyType;
use Symfony\Component\Validator\Constraint;

class MoneyValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param \Symfony\Component\Validator\Constraint|Money $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value instanceof MoneyType) {
            $value = $value->getCount();
        }

        if (null === $value || '' === $value) {
            if ($constraint->notBlank) {
                $this->context->addViolation(
                    $constraint->messageNotBlank
                );
            }
            return;
        }

        $precision = (int) $constraint->precision;
        $divider = pow(10, $precision);

        if ($value < 0 || ($value == 0 && $constraint->zero === false)) {
            $this->context->addViolation(
                $constraint->messageNegative,
                array(
                    '{{ value }}' => $value
                )
            );
        }

        $money = $value / $divider;

        if (null !== $constraint->max && $value > $constraint->max) {
            $this->context->addViolation(
                $constraint->messageMax,
                array(
                    '{{ value }}' => $money,
                    '{{ limit }}' => $constraint->max / $divider,
                )
            );
        }

        if ($value - floor($value) > 0) {
            $this->context->addViolation(
                $constraint->messagePrecision,
                array(
                    '{{ value }}' => $money,
                    '{{ precision }}' => $precision
                )
            );
        }
    }
}
