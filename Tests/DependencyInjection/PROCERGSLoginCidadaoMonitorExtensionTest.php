<?php

namespace PROCERGS\LoginCidadao\MonitorBundle\Tests\DependencyInjection;

use PROCERGS\LoginCidadao\MonitorBundle\Check\Wsdl;
use PROCERGS\LoginCidadao\MonitorBundle\DependencyInjection\PROCERGSLoginCidadaoMonitorExtension;
use SebastianBergmann\GlobalState\RuntimeException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class PROCERGSLoginCidadaoMonitorExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadSetParameters()
    {
        $container = $this->createContainer();
        $circuitBreaker = $this->registerCircuitBreaker($container);
        $container->registerExtension(new PROCERGSLoginCidadaoMonitorExtension());
        $container->loadFromExtension(
            'procergs_login_cidadao_monitor',
            [
                'circuit_breaker' => 'circuit_breaker',
                'checks' => [
                    'wsdl' => [
                        'test1' => [
                            'url' => 'https://lerolero',
                            'circuit_breaker_service_id' => 'circuit_wsdl',
                        ],
                        'test2' => ['url' => 'https://lerolero'],
                    ],
                ],
            ]
        );
        $this->compileContainer($container);

        /** @var Wsdl $service1 */
        $service1 = $container->get('procergs.monitor.check.wsdl.test1');
        $service2 = $container->get('procergs.monitor.check.wsdl.test2');

        $this->assertInstanceOf('Ejsmont\CircuitBreaker\CircuitBreakerInterface', $circuitBreaker);
        $this->assertInstanceOf('PROCERGS\LoginCidadao\MonitorBundle\Check\Wsdl', $service1);
        $this->assertInstanceOf('PROCERGS\LoginCidadao\MonitorBundle\Check\Wsdl', $service2);
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

    private function registerCircuitBreaker(ContainerBuilder $container)
    {
        $circuitBreaker = $this->getMockBuilder('Ejsmont\CircuitBreaker\CircuitBreakerInterface')
            ->getMock();

        $container->register('circuit_breaker', $circuitBreaker);
        return $circuitBreaker;
    }
}
