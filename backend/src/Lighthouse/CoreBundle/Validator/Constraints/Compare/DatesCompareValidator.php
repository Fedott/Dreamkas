<?php

namespace Lighthouse\CoreBundle\Validator\Constraints\Compare;

use DateTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class DatesCompareValidator extends CompareValidator
{
    /**
     * @param $value
     * @return DateTime
     * @throws \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    protected function normalizeFieldValue($value)
    {
        if (!$value instanceof DateTime) {
            throw new UnexpectedTypeException($value, '\DateTime');
        }
        return $value;
    }

    /**
     * @param Constraint $constraint
     * @param $value
     * @return mixed
     */
    protected function formatMessageValue(Constraint $constraint, $value)
    {
        return $value->format($constraint->dateFormat);
    }
}
