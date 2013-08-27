<?php

namespace Lighthouse\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('lighthouse_core');

        $nodeBuilder = $rootNode->children();

        $this->addJobConfig($nodeBuilder);
        $this->addMoneyConfig($nodeBuilder);
        $this->addRoundingConfig($nodeBuilder);

        return $treeBuilder;
    }

    /**
     * @param NodeBuilder $nodeBuilder
     */
    protected function addJobConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('job')
                ->children()
                    ->arrayNode('worker')
                        ->children()
                            ->integerNode('max_runtime')
                                ->info('Worker max runtime in seconds')
                                ->example(600)
                                ->defaultValue(600)
                            ->end()
                            ->integerNode('reserve_timeout')
                                ->info('Time to wait for a new job in seconds')
                                ->example(60)
                                ->defaultValue(60)
                            ->end()
                        ->end()
                    ->end()
                    ->scalarNode('tube_prefix')
                        ->info('Tube name prefix not to mess with another hosts')
                        ->defaultValue('')
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param NodeBuilder $nodeBuilder
     */
    protected function addMoneyConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('money')
                ->children()
                    ->integerNode('precision')
                        ->defaultValue(2)
        ;
    }

    /**
     * @param NodeBuilder $nodeBuilder
     */
    protected function addRoundingConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('rounding')
                ->children()
                    ->scalarNode('default')
                        ->info('Default rounding used in system')
                        ->defaultValue('nearest1')
        ;
    }
}
