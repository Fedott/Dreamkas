<?php

namespace Lighthouse\CoreBundle\Serializer\Metadata\Driver;

use Doctrine\Common\Persistence\ManagerRegistry;
use FOS\RestBundle\Util\Inflector\DoctrineInflector;
use JMS\Serializer\Metadata\Driver\AbstractDoctrineTypeDriver;
use Metadata\Driver\DriverInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\DiExtraBundle\Annotation as DI;
use ReflectionClass;

/**
 * @DI\Service("lighthouse.core.serializer.metadata.driver.document", public=false)
 */
class DocumentDriver extends AbstractDoctrineTypeDriver
{
    /**
     * @var DoctrineInflector
     */
    protected $inflector;

    /**
     * @DI\InjectParams({
     *      "delegate" = @DI\Inject("jms_serializer.metadata.chain_driver"),
     *      "registry" = @DI\Inject("doctrine_mongodb"),
     *      "inflector"= @DI\Inject("fos_rest.inflector.doctrine")
     * })
     * @param DriverInterface $delegate
     * @param ManagerRegistry $registry
     * @param DoctrineInflector $inflector
     */
    public function __construct(DriverInterface $delegate, ManagerRegistry $registry, DoctrineInflector $inflector)
    {
        parent::__construct($delegate, $registry);

        $this->inflector = $inflector;
    }

    /**
     * @param ReflectionClass $class
     * @return ClassMetadata
     */
    public function loadMetadataForClass(ReflectionClass $class)
    {
        $metadata = parent::loadMetadataForClass($class);

        if ($class->isSubclassOf('\\Lighthouse\\CoreBundle\\Document\\AbstractDocument')) {
            $this->processDocumentMetadata($metadata);
        } elseif ($class->isSubclassOf('\\Lighthouse\\CoreBundle\\Document\\AbstractCollection')) {
            $this->processCollectionMetadata($metadata);
        }
        return $metadata;
    }

    /**
     * @param ClassMetadata $metadata
     */
    protected function processCollectionMetadata(ClassMetadata $metadata)
    {
        if (null === $metadata->xmlRootName) {
            $className = $metadata->reflection->getShortName();
            $metadata->xmlRootName = $this->getCollectionTagName($className);
        }
    }

    /**
     * @param ClassMetadata $metadata
     */
    protected function processDocumentMetadata(ClassMetadata $metadata)
    {
        if (null === $metadata->xmlRootName) {
            $className = $metadata->reflection->getShortName();
            $metadata->xmlRootName = $this->getItemTagName($className);
        }
    }

    /**
     * @param string $className
     * @return string
     */
    protected function getCollectionTagName($className)
    {
        $className = str_replace('Collection', '', $className);
        $className = lcfirst($className);
        return $this->inflector->pluralize($className);
    }

    /**
     * @param $className
     * @return string
     */
    protected function getItemTagName($className)
    {
        $className = lcfirst($className);
        return $className;
    }
}
