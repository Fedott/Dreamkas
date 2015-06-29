<?php

namespace Lighthouse\CoreBundle;

use Lighthouse\CoreBundle\DependencyInjection\Compiler\ModifyNewRelicRequestListenerPriority;
use Lighthouse\CoreBundle\DependencyInjection\Compiler\AddReferenceProvidersPass;
use Lighthouse\CoreBundle\DependencyInjection\Compiler\AddRoundingsToManagerPass;
use Lighthouse\CoreBundle\DependencyInjection\Compiler\MongoDBDocumentManagerPass;
use Lighthouse\CoreBundle\MongoDB\Types\DateTimeTZType;
use Lighthouse\CoreBundle\MongoDB\Types\DateTimeUTCType;
use Lighthouse\CoreBundle\MongoDB\Types\MoneyType;
use Lighthouse\CoreBundle\MongoDB\Types\QuantityType;
use Lighthouse\CoreBundle\MongoDB\Types\TimestampType;
use Samba\SambaStreamWrapper;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Doctrine\ODM\MongoDB\Types\Type;

class LighthouseCoreBundle extends Bundle
{
    /**
     *
     */
    public function __construct()
    {
        $this->registerMongoTypes();
        $this->addStreamWrappers();
    }

    protected function registerMongoTypes()
    {
        Type::registerType(QuantityType::NAME, QuantityType::getClassName());
        Type::registerType(MoneyType::NAME, MoneyType::getClassName());
        Type::registerType(TimestampType::NAME, TimestampType::getClassName());
        Type::registerType(DateTimeTZType::NAME, DateTimeTZType::getClassName());
        Type::registerType(DateTimeUTCType::NAME, DateTimeUTCType::getClassName());
    }

    protected function addStreamWrappers()
    {
        if (!SambaStreamWrapper::is_registered()) {
            SambaStreamWrapper::register();
        }
    }

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ModifyNewRelicRequestListenerPriority());
        $container->addCompilerPass(new AddRoundingsToManagerPass());
        $container->addCompilerPass(new AddReferenceProvidersPass());
        $container->addCompilerPass(new MongoDBDocumentManagerPass());
    }
}
