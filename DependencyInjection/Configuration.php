<?php

namespace PQstudio\RateLimitBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('pq_rate_limit', 'array');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $rootNode
            ->children()
            ->arrayNode('limits')
                ->cannotBeOverwritten()
                ->prototype('array')
                    ->children()
                    ->scalarNode('path')->defaultNull()->info('URL path info')->end()
                    ->arrayNode('method')
                        ->beforeNormalization()->ifString()->then(function($v) { return preg_split('/\s*,\s*/', $v); })->end()
                        ->useAttributeAsKey('name')
                        ->prototype('scalar')->end()
                        ->info('HTTP method')
                    ->end()
                    ->arrayNode('ips')
                        ->beforeNormalization()->ifString()->then(function($v) { return preg_split('/\s*,\s*/', $v); })->end()
                        ->useAttributeAsKey('name')
                        ->prototype('scalar')->end()
                        ->info('List of ips')
                    ->end()
                    ->arrayNode('attributes')
                        ->addDefaultsIfNotSet()
                        ->cannotBeEmpty()
                        ->treatNullLike(array())
                        ->info('Request attributes')
                    ->end()
                    ->scalarNode('domain')->defaultNull()->info('depreciated, use host instead')->end()
                    ->scalarNode('host')->defaultNull()->info('URL host name')->end()
                    ->scalarNode('controller')->defaultNull()->info('controller action name')->end()

                    ->scalarNode('limit')->defaultNull()->info('Number of requests allowed')->end()
                    ->scalarNode('time')->defaultNull()->info('Seconds for limit')->end()
                    ->booleanNode('captcha')->defaultFalse()->info('Is captcha required after limit?')->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
