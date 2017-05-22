<?php

namespace PROCERGS\LoginCidadao\MonitorBundle\Tests\DependencyInjection;

use PROCERGS\LoginCidadao\MonitorBundle\DependencyInjection\PROCERGSLoginCidadaoMonitorExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class PROCERGSLoginCidadaoMonitorExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadSetParameters()
    {
        $cbServiceId = 'my.service.id';
        $cbLabel = 'My CB Monitor';
        $cbService = new Definition('Eljam\CircuitBreaker\Breaker');
        $cbService->setArguments(['dummy']);

        $container = $this->createContainer();
        $container->setDefinition($cbServiceId, $cbService);
        $container->registerExtension(new PROCERGSLoginCidadaoMonitorExtension());
        $container->loadFromExtension(
            'procergs_login_cidadao_monitor',
            [
                'checks' => [
                    'wsdl' => [
                        'test1' => [
                            'url' => 'https://lerolero',
                        ],
                        'test2' => ['url' => 'https://lerolero'],
                    ],
                    'circuit_breaker' => [
                        'my_cb' => [
                            'label' => $cbLabel,
                            'service_id' => $cbServiceId,
                        ],
                    ],
                ],
            ]
        );
        $this->compileContainer($container);

        $service1 = $container->get('procergs.monitor.check.wsdl.test1');
        $service2 = $container->get('procergs.monitor.check.wsdl.test2');
        $service3 = $container->get('procergs.monitor.check.circuit_breaker.my_cb');

        $this->assertInstanceOf('PROCERGS\LoginCidadao\MonitorBundle\Check\Wsdl', $service1);
        $this->assertInstanceOf('PROCERGS\LoginCidadao\MonitorBundle\Check\Wsdl', $service2);
        $this->assertInstanceOf('PROCERGS\LoginCidadao\MonitorBundle\Check\CircuitBreaker', $service3);
        $this->assertEquals($cbLabel, $service3->getLabel());
    }

    /**
     * @return ContainerBuilder
     */
    private function createContainer()
    {
        $container = new ContainerBuilder(
            new ParameterBag(
                array(
                    'kernel.cache_dir' => __DIR__,
                    'kernel.root_dir' => __DIR__.'/Fixtures',
                    'kernel.charset' => 'UTF-8',
                    'kernel.debug' => false,
                    'kernel.bundles' => array('PROCERGSLoginCidadaoMonitorBundle' => 'PROCERGS\\LoginCidadao\\MonitorBundle\\PROCERGSLoginCidadaoMonitorBundle'),
                )
            )
        );

        return $container;
    }

    private function compileContainer(ContainerBuilder $container)
    {
        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();
    }
}
