<?php

namespace PROCERGS\LoginCidadao\MonitorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class PROCERGSLoginCidadaoMonitorExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (!empty($config['checks'])) {
            if (!empty($config['checks']['wsdl'])) {
                $this->registerWsdlChecks($config['checks']['wsdl'], $container);
            }
            if (!empty($config['checks']['circuit_breaker'])) {
                $this->registerCBChecks($config['checks']['circuit_breaker'], $container);
            }
        }
    }

    public function getAlias()
    {
        return 'procergs_login_cidadao_monitor';
    }

    private function registerWsdlChecks($checks, ContainerBuilder $container)
    {
        foreach ($checks as $name => $options) {
            $check = new Definition('PROCERGS\LoginCidadao\MonitorBundle\Check\Wsdl');
            $check->setArguments([$options['url'], $options['label'], $options['verify_https']]);
            $check->addTag('liip_monitor.check', ['alias' => "wsdl_check_$name"]);
            $container->setDefinition("procergs.monitor.check.wsdl.$name", $check);
        }
    }

    private function registerCBChecks($checks, ContainerBuilder $container)
    {
        foreach ($checks as $name => $options) {
            $check = new Definition('PROCERGS\LoginCidadao\MonitorBundle\Check\CircuitBreaker');
            $check->setArguments([new Reference($options['service_id']), $options['label']]);
            $check->addTag('liip_monitor.check', ['alias' => "circuit_breaker_check_{$name}"]);
            $container->setDefinition("procergs.monitor.check.circuit_breaker.{$name}", $check);
        }
    }
}
