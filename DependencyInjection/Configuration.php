<?php

namespace Kitpages\DataGridBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kitpages_data_grid');

        $this->addGridConfiguration($rootNode);
        $this->addPaginatorConfiguration($rootNode);

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }

    private function addGridConfiguration(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('grid')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('hydrator_class')
                            ->defaultValue('\Kitpages\DataGridBundle\Hydrators\DataGridHydrator')
                        ->end()
                        ->scalarNode('default_twig')
                            ->cannotBeEmpty()
                            ->defaultValue('KitpagesDataGridBundle:Grid:grid.html.twig')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

    }
    private function addPaginatorConfiguration(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('paginator')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('default_twig')
                            ->cannotBeEmpty()
                            ->defaultValue('KitpagesDataGridBundle:Paginator:paginator.html.twig')
                        ->end()
                        ->scalarNode('item_count_in_page')
                            ->cannotBeEmpty()
                            ->defaultValue(50)
                        ->end()
                        ->scalarNode('visible_page_count_in_paginator')
                            ->cannotBeEmpty()
                            ->defaultValue(5)
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

    }
}
