<?php

namespace Lighthouse\CoreBundle\Validator\Constraints\Compare;

use Lighthouse\CoreBundle\Document\AbstractDocument;
use Lighthouse\CoreBundle\Validator\Constraints\ConstraintValidator;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

abstract class CompareValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param \Symfony\Component\Validator\Constraint|NumbersCompare $constraint
     * @throws \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof AbstractDocument) {
            throw new UnexpectedTypeException($value, AbstractDocument::getClassName());
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        $minFieldValue = $accessor->getValue($value, $constraint->minField);
        $maxFieldValue = $accessor->getValue($value, $constraint->maxField);

        if ($this->isNull($minFieldValue) || $this->isNull($maxFieldValue)) {
            return;
        }

        $normalizedMinFieldValue = $this->normalizeFieldValue($minFieldValue);
        $normalizedMaxFieldValue = $this->normalizeFieldValue($maxFieldValue);

        if ($this->doCompare($normalizedMinFieldValue, $normalizedMaxFieldValue, $constraint)) {
            $this->context->addViolationAt(
                $constraint->minField,
                $constraint->message,
                array(
                    '{{ firstValue }}' => $this->formatMessageValue($minFieldValue, $constraint),
                    '{{ secondValue }}' => $this->formatMessageValue($maxFieldValue, $constraint),
                )
            );
        }
    }

    /**
     * @param mixed $minValue
     * @param mixed $maxValue
     * @return bool
     */
    protected function doCompare($minValue, $maxValue)
    {
        return $minValue > $maxValue;
    }

    /**
     * @param $value
     * @return integer|float
     * @throws \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    abstract protected function normalizeFieldValue($value);

    /**
     * @param Constraint $constraint
     * @param $value
     * @return mixed
     */
    abstract protected function formatMessageValue($value, Constraint $constraint);
}
