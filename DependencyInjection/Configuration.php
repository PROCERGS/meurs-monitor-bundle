<?php

namespace PROCERGS\LoginCidadao\MonitorBundle\DependencyInjection;

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
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('procergs_monitor');

        $rootNode
            ->children()
                ->arrayNode('checks')
                    ->children()
                        ->arrayNode('wsdl')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('label')
                                        ->info('The name of this check')
                                        ->example('My WSDL Check')
                                        ->defaultNull()
                                    ->end()
                                    ->scalarNode('url')
                                        ->info('URL where the WDSL can be found.')
                                        ->example('https://some.domain.tld/service.wsdl')
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                    ->end()
                                    ->booleanNode('verify_https')
                                        ->info('When false, errors such as invalid TLS certificates will be ignored')
                                        ->defaultTrue()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('circuit_breaker')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('label')
                                        ->info('The name of this check')
                                        ->example('My Circuit Breaker Check')
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                    ->end()
                                    ->scalarNode('service_id')
                                        ->info('The service ID for the circuit breaker you wish to monitor')
                                        ->example('my.cb.service')
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
