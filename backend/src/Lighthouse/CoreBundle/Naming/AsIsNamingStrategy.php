<?php

namespace Lighthouse\CoreBundle\Naming;

use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("lighthouse.core.naming.as_is")
 */
class AsIsNamingStrategy implements PropertyNamingStrategyInterface
{
    /**
     * Translates the name of the property to the serialized version.
     *
     * @param PropertyMetadata $property
     *
     * @return string
     */
    public function translateName(PropertyMetadata $property)
    {
        return $property->name;
    }
}