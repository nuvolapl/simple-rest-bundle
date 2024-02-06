<?php

declare(strict_types=1);

namespace Nuvola\SimpleRestBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Nuvola\SimpleRestBundle\ArgumentResolver\ContentArgumentResolver;

final class SimpleRestExtension extends ConfigurableExtension
{
    public function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.yaml');

        foreach (['filter', 'pagination'] as $name) {
            $definition = $container->getDefinition("nuvola.argument_resolver.{$name}_query_string");
            $definition->setBindings(
                [
                    '$denormalizer'            => new Reference($mergedConfig['serialization']['serializer_service']),
                    '$validator'               => new Reference($mergedConfig['validation']['validator_service']),
                    '$isEnabled'               => $mergedConfig[$name]['enabled'],
                    '$isValidationEnabled'     => $mergedConfig[$name]['validation_enabled'],
                    '$queryStringKeyName'      => $mergedConfig[$name]['query_string_key_name'],
                    '$validationAttributeName' => $mergedConfig['validation']['attribute_name'],
                ]
            );
        }

        $definition = $container->getDefinition(ContentArgumentResolver::class);
        $definition->setBindings(
            [
                '$serializer'              => new Reference($mergedConfig['serialization']['serializer_service']),
                '$validator'               => new Reference($mergedConfig['validation']['validator_service']),
                '$isEnabled'               => $mergedConfig['payload']['enabled'],
                '$isValidationEnabled'     => $mergedConfig['payload']['validation_enabled'],
                '$attributeName'           => $mergedConfig['payload']['attribute_name'],
                '$validationAttributeName' => $mergedConfig['validation']['attribute_name'],
            ]
        );
    }

    public function getAlias(): string
    {
        return 'simple_rest';
    }
}
