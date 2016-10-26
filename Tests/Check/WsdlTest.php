<?php
/**
 * This file is part of the login-cidadao project or it's bundles.
 *
 * (c) Guilherme Donato <guilhermednt on github>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PROCERGS\LoginCidadao\MonitorBundle\Tests\Check;

use PROCERGS\LoginCidadao\MonitorBundle\Check\Wsdl;

class WsdlTest extends \PHPUnit_Framework_TestCase
{
    public function testStringToSetCircuitBreaker()
    {
        try {
            $this->getWsdlCheck()->setCircuitBreaker('foo');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\RuntimeException', $e);
        }
    }

    public function testObjectToSetCircuitBreaker()
    {
        try {
            $this->getWsdlCheck()->setCircuitBreaker(new \DateTime());
        } catch (\Exception $e) {
            $this->assertInstanceOf('\RuntimeException', $e);
        }
    }

    public function testNullToSetCircuitBreaker()
    {
        try {
            $this->getWsdlCheck()->setCircuitBreaker(null);
        } catch (\Exception $e) {
            $this->assertInstanceOf('\RuntimeException', $e);
        }
    }

    public function testSetCircuitBreaker()
    {
        $circuitBreaker = $this->getMockBuilder('Ejsmont\CircuitBreaker\CircuitBreakerInterface')->getMock();

        try {
            $this->getWsdlCheck()->setCircuitBreaker($circuitBreaker);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testCircuitBreakerNotification()
    {
        $circuitBreaker = $this->prophesize('Ejsmont\CircuitBreaker\CircuitBreakerInterface');
        $circuitBreaker->reportFailure('service')->shouldBeCalled();

        $checker = $this->getWsdlCheck();

        $checker->setCircuitBreaker($circuitBreaker->reveal())
            ->setCircuitBreakerServiceId('service');

        $checker->check();
    }

    public function testCheckFails()
    {
        $checker = $this->getWsdlCheck();
        $result = $checker->check();

        $this->assertInstanceOf('ZendDiagnostics\Result\Failure', $result);
    }

    private function getWsdlCheck($url = 'https://dum.my/service.wsdl', $label = null, $ignoreHttpsChecks = false)
    {
        return new Wsdl($url, $label, $ignoreHttpsChecks);
    }
}
