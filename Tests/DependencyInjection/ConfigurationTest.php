<?php

namespace PROCERGS\LoginCidadao\MonitorBundle\Tests\DependencyInjection;


use PROCERGS\LoginCidadao\MonitorBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

/**
 * @group unit
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetConfigTreeBuilder()
    {
        $configuration = new Configuration();
        $this->assertInstanceOf(
            'Symfony\Component\Config\Definition\Builder\TreeBuilder',
            $configuration->getConfigTreeBuilder()
        );
    }

    public function testDefaultConfig()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), []);

        $this->assertEquals(self::getBundleDefaultConfig(), $config);
    }

    public function testNonDefaultConfig()
    {
        $configs = [
            [
                'checks' => [
                    'wsdl' => [
                        'test1' => ['url' => 'https://lerolero'],
                    ],
                ],
            ],
        ];

        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), $configs);

        $expected = self::getBundleDefaultConfig();
        $expected['checks'] = [
            'wsdl' => [
                'test1' => [
                    'url' => 'https://lerolero',
                    'label' => null,
                    'ignore_https_errors' => false,
                ],
            ],
        ];
        $this->assertEquals($expected, $config);
    }

    protected static function getBundleDefaultConfig()
    {
        return [
        ];
    }
}
