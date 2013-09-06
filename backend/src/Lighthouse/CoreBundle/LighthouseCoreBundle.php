<?php

namespace Lighthouse\CoreBundle;

use Lighthouse\CoreBundle\Command\CommandManager;
use Lighthouse\CoreBundle\DependencyInjection\Compiler\AddCommandAsServicePass;
use Lighthouse\CoreBundle\DependencyInjection\Compiler\AddJobWorkersPass;
use Lighthouse\CoreBundle\DependencyInjection\Compiler\AddReferenceProvidersPass;
use Lighthouse\CoreBundle\DependencyInjection\Compiler\AddRoundingsToManagerPass;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Doctrine\ODM\MongoDB\Types\Type;

class LighthouseCoreBundle extends Bundle
{
    public function __construct()
    {
        $this->registerMongoTypes();
        $this->addStreamWrappers();
    }

    protected function registerMongoTypes()
    {
        Type::registerType('money', 'Lighthouse\CoreBundle\Types\MongoDB\MoneyType');
        Type::registerType('timestamp', 'Lighthouse\CoreBundle\Types\MongoDB\TimestampType');
    }

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddCommandAsServicePass());
        $container->addCompilerPass(new AddRoundingsToManagerPass());
        $container->addCompilerPass(new AddReferenceProvidersPass());
        $container->addCompilerPass(new AddJobWorkersPass());
    }

    /**
     * @param Application $application
     */
    public function registerCommands(Application $application)
    {
        /* @var CommandManager $commandManager */
        $commandManager = $this->container->get('lighthouse.core.command.manager');
        $application->addCommands($commandManager->getAll());
    }

    public function addStreamWrappers()
    {
        stream_wrapper_register('smb', 'Lighthouse\CoreBundle\Samba\SambaStreamWrapper');
    }
}
