<?php

namespace Lighthouse\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class LighthouseCoreExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('lighthouse.core.precision.money', $config['precision']['money']);
        $container->setParameter('lighthouse.core.precision.quantity', $config['precision']['quantity']);

        $container->setParameter('lighthouse.core.rounding.default', $config['rounding']['default']);

        $container->setParameter('openstack.selectel.auth_url', $config['selectel']['auth']['url']);
        $container->setParameter('openstack.selectel.secret.username', $config['selectel']['auth']['username']);
        $container->setParameter('openstack.selectel.secret.password', $config['selectel']['auth']['password']);
        $container->setParameter('openstack.selectel.storage.container.name', $config['selectel']['container']);
        $container->setParameter('openstack.selectel.options', $config['selectel']['options']);
    }
}
