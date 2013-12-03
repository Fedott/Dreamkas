<?php

namespace Lighthouse\CoreBundle\Serializer\Handler;

use JMS\Serializer\Context;
use JMS\Serializer\VisitorInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Lighthouse\CoreBundle\DataTransformer\FloatViewTransformer;
use Lighthouse\CoreBundle\DataTransformer\MoneyModelTransformer;
use Lighthouse\CoreBundle\Types\Numeric\Money;

/**
 * @DI\Service("lighthouse.core.serializer.handler.money")
 * @DI\Tag(
 *      "jms_serializer.handler",
 *      attributes={
 *          "type": "Lighthouse\CoreBundle\Types\Numeric\Money",
 *          "format": "json",
 *          "direction": "serialization",
 *          "method": "serializeMoney"
 *      }
 * )
 * @DI\Tag(
 *      "jms_serializer.handler",
 *      attributes={
 *          "type": "Lighthouse\CoreBundle\Types\Numeric\Money",
 *          "format": "xml",
 *          "direction": "serialization",
 *          "method": "serializeMoney"
 *      }
 * )
 */
class MoneyHandler
{
    /**
     * @var MoneyModelTransformer
     */
    protected $modelTransformer;

    /**
     * @var FloatViewTransformer
     */
    protected $viewTransformer;

    /**
     * @DI\InjectParams({
     *      "modelTransformer" = @DI\Inject("lighthouse.core.data_transformer.money_model"),
     *      "viewTransformer" = @DI\Inject("lighthouse.core.data_transformer.float_view")
     * })
     * @param MoneyModelTransformer $modelTransformer
     * @param FloatViewTransformer $viewTransformer
     */
    public function __construct(MoneyModelTransformer $modelTransformer, FloatViewTransformer $viewTransformer)
    {
        $this->modelTransformer = $modelTransformer;
        $this->viewTransformer = $viewTransformer;
    }

    /**
     * @param VisitorInterface $visitor
     * @param Money $value
     * @param array $type
     * @param Context $context
     * @return string|null
     */
    public function serializeMoney(VisitorInterface $visitor, Money $value, array $type, Context $context)
    {
        if ($value->isNull()) {
            $serialized = $visitor->visitNull($value, $type, $context);
        } else {
            $serialized = $visitor->visitDouble($value->toNumber(), $type, $context);
        }
        return $serialized;
    }
}
