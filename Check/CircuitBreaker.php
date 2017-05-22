<?php
/**
 * This file is part of the login-cidadao project or it's bundles.
 *
 * (c) Guilherme Donato <guilhermednt on github>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PROCERGS\LoginCidadao\MonitorBundle\Check;

use Eljam\CircuitBreaker\Breaker;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\Success;

class CircuitBreaker implements CheckInterface
{
    /** @var Breaker */
    private $circuitBreaker;

    /** @var string */
    private $label;

    /**
     * CircuitBreakerCheck constructor.
     * @param Breaker $circuitBreaker
     * @param $label
     */
    public function __construct(Breaker $circuitBreaker, $label)
    {
        $this->circuitBreaker = $circuitBreaker;
        $this->label = $label;
    }

    /**
     * Perform the actual check and return a ResultInterface
     *
     * @return ResultInterface
     */
    public function check()
    {
        try {
            return $this->circuitBreaker->protect(
                function () {
                    return new Success();
                }
            );
        } catch (\Exception $e) {
            return new Failure();
        }
    }

    /**
     * Return a label describing this test instance.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }
}
