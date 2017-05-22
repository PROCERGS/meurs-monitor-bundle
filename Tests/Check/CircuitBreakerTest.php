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

use Eljam\CircuitBreaker\Breaker;
use Eljam\CircuitBreaker\Exception\CircuitOpenException;
use PROCERGS\LoginCidadao\MonitorBundle\Check\CircuitBreaker;

class CircuitBreakerTest extends \PHPUnit_Framework_TestCase
{
    public function testSuccess()
    {
        $label = 'Dummy Label';
        /** @var Breaker|\PHPUnit_Framework_MockObject_MockObject $cb */
        $cb = $this->getMockBuilder('Eljam\CircuitBreaker\Breaker')
            ->disableOriginalConstructor()
            ->getMock();
        $cb->expects($this->once())->method('protect')->willReturnCallback(
            function (\Closure $closure) {
                return $closure();
            }
        );

        $check = new CircuitBreaker($cb, $label);
        $response = $check->check();

        $this->assertInstanceOf('ZendDiagnostics\Result\Success', $response);
    }

    public function testFailure()
    {
        $label = 'Dummy Label';
        /** @var Breaker|\PHPUnit_Framework_MockObject_MockObject $cb */
        $cb = $this->getMockBuilder('Eljam\CircuitBreaker\Breaker')
            ->disableOriginalConstructor()
            ->getMock();
        $cb->expects($this->once())->method('protect')->willThrowException(new CircuitOpenException());

        $check = new CircuitBreaker($cb, $label);
        $response = $check->check();

        $this->assertInstanceOf('ZendDiagnostics\Result\Failure', $response);
    }
}
