<?php

declare(strict_types=1);

namespace Nuvola\SimpleRestBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('simple_rest');
        $treeBuilder->getRootNode()
            ->isRequired()
            ->children()
                ->arrayNode('filter')
                    ->isRequired()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->booleanNode('validation_enabled')->defaultTrue()->end()
                        ->scalarNode('query_string_key_name')->defaultValue('filter')->end()
                    ->end()
                ->end() // filter
                ->arrayNode('pagination')
                    ->isRequired()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->booleanNode('validation_enabled')->defaultTrue()->end()
                        ->scalarNode('query_string_key_name')->defaultValue('pagination')->end()
                    ->end()
                ->end() // pagination
                ->arrayNode('payload')
                    ->isRequired()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->booleanNode('validation_enabled')->defaultTrue()->end()
                        ->scalarNode('attribute_name')->defaultValue('payload')->end()
                    ->end()
                ->end() // payload
                ->arrayNode('validation')
                    ->isRequired()
                    ->children()
                        ->scalarNode('validator_service')->defaultValue('validator')->end()
                        ->scalarNode('attribute_name')->defaultValue('violations')->end()
                    ->end()
                ->end() // validation
                ->arrayNode('serialization')
                    ->isRequired()
                    ->children()
                        ->scalarNode('serializer_service')->defaultValue('serializer')->end()
                    ->end()
                ->end() // serialization
            ->end()
        ;

        return $treeBuilder;
    }
}
