<?php

namespace Lighthouse\CoreBundle\Tests\Validator\Constraints;

use Lighthouse\CoreBundle\DataTransformer\MoneyModelTransformer;
use Lighthouse\CoreBundle\Types\Money;
use Lighthouse\CoreBundle\Validator\Constraints\ClassMoneyRange;
use Lighthouse\CoreBundle\Validator\Constraints\ClassMoneyRangeValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\ExecutionContextInterface;
use stdClass;

class ClassMoneyRangeValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExecutionContextInterface
     */
    protected $context;

    /**
     * @var ClassMoneyRangeValidator
     */
    protected $validator;

    public function setUp()
    {
        $this->context = $this->getMock('Symfony\Component\Validator\ExecutionContext', array(), array(), '', false);
        $moneyTransformer = new MoneyModelTransformer();
        $this->validator = new ClassMoneyRangeValidator($moneyTransformer);
        $this->validator->initialize($this->context);
    }

    public function tearDown()
    {
        $this->context = null;
        $this->validator = null;
    }

    public function testLimitFields()
    {
        $value = new stdClass;
        $value->price = new Money(1011);
        $value->minPrice = new Money(509);
        $value->maxPrice = new Money(1234);

        $options = array(
            'field' => 'price',
            'gt' => 'minPrice',
            'lt' => 'maxPrice',
        );

        $this
            ->context
            ->expects($this->never())
            ->method('addViolation');

        $constraint = new ClassMoneyRange($options);
        $this->validator->validate($value, $constraint);
    }

    public function testNestedLimitFields()
    {
        $value = new stdClass;
        $value->price = new Money(1011);
        $value->parent = new stdClass;
        $value->parent->minPrice = new Money(509);
        $value->parent->maxPrice = new Money(1234);

        $options = array(
            'field' => 'price',
            'gt' => 'parent.minPrice',
            'lt' => 'parent.maxPrice',
        );

        $this
            ->context
            ->expects($this->never())
            ->method('addViolation');

        $constraint = new ClassMoneyRange($options);
        $this->validator->validate($value, $constraint);
    }

    public function testNullLimitField()
    {
        $value = new stdClass;
        $value->price = new Money(1011);
        $value->minPrice = null;

        $options = array(
            'field' => 'price',
            'gt' => 'minPrice',
        );

        $this
            ->context
            ->expects($this->never())
            ->method('addViolation');

        $constraint = new ClassMoneyRange($options);
        $this->validator->validate($value, $constraint);
    }

    public function testNullValueField()
    {
        $value = new stdClass;
        $value->price = null;
        $value->minPrice = new Money(1011);

        $options = array(
            'field' => 'price',
            'gt' => 'minPrice',
        );

        $this
            ->context
            ->expects($this->never())
            ->method('addViolation');

        $constraint = new ClassMoneyRange($options);
        $this->validator->validate($value, $constraint);
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testLimitIsNotMoney()
    {
        $value = new stdClass;
        $value->price = new Money(1011);
        $value->minPrice = 509;

        $options = array(
            'field' => 'price',
            'gt' => 'minPrice',
        );

        $constraint = new ClassMoneyRange($options);
        $this->validator->validate($value, $constraint);
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testValueIsNotMoney()
    {
        $value = new stdClass;
        $value->price = 1011;
        $value->minPrice = new Money(509);

        $options = array(
            'field' => 'price',
            'gt' => 'minPrice',
        );

        $constraint = new ClassMoneyRange($options);
        $this->validator->validate($value, $constraint);
    }

    public function testValueAndLimitFormatInViolationMessage()
    {
        $value = new stdClass;
        $value->price = new Money(1011);
        $value->minPrice = new Money(1509);

        $options = array(
            'field' => 'price',
            'gt' => 'minPrice',
        );

        $this
            ->context
            ->expects($this->once())
            ->method('addViolation')
            ->with(
                'lighthouse.validation.errors.range.gt',
                array(
                    '{{ value }}' => '10.11',
                    '{{ limit }}' => '15.09'
                )
            );

        $constraint = new ClassMoneyRange($options);
        $this->validator->validate($value, $constraint);
    }

    public function testConstraintTarget()
    {
        $options = array(
            'field' => 'price',
            'lt' => 'minPrice',
        );

        $constraint = new ClassMoneyRange($options);

        $this->assertEquals(Constraint::CLASS_CONSTRAINT, $constraint->getTargets());
    }
}
