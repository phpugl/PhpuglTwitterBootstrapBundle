<?php

namespace Phpugl\TwitterBootstrapBundle\DependencyInjection;

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

        $rootNode = $treeBuilder->root('phpugl_twitter_bootstrap');

        $this->addConfigSection($rootNode);
        $this->addLessSection($rootNode);
        $this->addImagesSection($rootNode);
        $this->addJavascriptSection($rootNode);

        return $treeBuilder;
    }

    private function addConfigSection(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->arrayNode('config')
                    ->children()
                        ->scalarNode('twitter_path')
                            ->info('path to vendor twitter bootrap')
                            ->defaultValue('%kernel.root_dir%/../vendor/twitter/bootstrap')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addLessSection(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->arrayNode('less')
                    ->children()
                        ->scalarNode('out')
                            ->info('output filename of the compiled less files')
                            ->defaultValue('bootstrap.css')
                        ->end()
                        ->arrayNode('files')
                            ->info('output filename of the compiled less files')
                            ->defaultValue(array(
                                'bootstrap.less',
                                'responsive.less'
                            ))
                            ->prototype('scalar')->end()
                        ->end()
                        ->variableNode('variables')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addImagesSection(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->arrayNode('images')
                    ->info('copy images file from twitter bootstrap')
                    ->children()
                        ->arrayNode('files')
                            ->defaultValue(array(
                                'glyphicons-halflings.png',
                                'glyphicons-halflings-white.png'
                            ))
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addJavascriptSection(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->arrayNode('javascript')
                    ->children()
                        ->scalarNode('out')->defaultValue('bootstrap.js')->end()
                        ->arrayNode('files')
                            ->defaultValue(array(
                                'bootstrap-transition.js',
                                'bootstrap-alert.js',
                                'bootstrap-modal.js',
                                'bootstrap-dropdown.js',
                                'bootstrap-scrollspy.js',
                                'bootstrap-tab.js',
                                'bootstrap-tooltip.js',
                                'bootstrap-popover.js',
                                'bootstrap-button.js',
                                'bootstrap-collapse.js',
                                'bootstrap-carousel.js',
                                'bootstrap-typeahead.js',
                                'bootstrap-affix.js'
                            ))
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
