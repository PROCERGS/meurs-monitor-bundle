<?php

namespace PROCERGS\LoginCidadao\MonitorBundle\Tests\DependencyInjection;

use PROCERGS\LoginCidadao\MonitorBundle\Check\Wsdl;
use PROCERGS\LoginCidadao\MonitorBundle\DependencyInjection\PROCERGSLoginCidadaoMonitorExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class PROCERGSLoginCidadaoMonitorExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadSetParameters()
    {
        $container = $this->createContainer();
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
                ],
            ]
        );
        $this->compileContainer($container);

        /** @var Wsdl $service1 */
        $service1 = $container->get('procergs.monitor.check.wsdl.test1');
        $service2 = $container->get('procergs.monitor.check.wsdl.test2');

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
}
