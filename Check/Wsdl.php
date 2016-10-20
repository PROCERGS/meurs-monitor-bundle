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


use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\Success;

class Wsdl implements CheckInterface
{
    /** @var string */
    private $label;

    /** @var string */
    private $wsdlUrl;

    /** @var resource */
    private $context;

    /** @var object */
    private $circuitBreaker;

    /** @var string */
    private $circuitBreakerServiceId;

    /**
     * Wsdl constructor.
     * @param $wsdlUrl
     * @param null $label
     * @param bool $ignoreHttpsChecks
     */
    public function __construct($wsdlUrl, $label = null, $ignoreHttpsChecks = false)
    {
        $this->label = $label;
        $this->wsdlUrl = $wsdlUrl;

        if ($ignoreHttpsChecks) {
            $this->context = stream_context_create(
                [
                    'ssl' => [
                        // set some SSL/TLS specific options
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ],
                ]
            );
        }
    }

    /**
     * Perform the actual check and return a ResultInterface
     *
     * @return ResultInterface
     */
    public function check()
    {
        try {
            @new \SoapClient(
                $this->wsdlUrl,
                array(
                    'cache_wsdl' => WSDL_CACHE_BOTH,
                    'trace' => true,
                    'stream_context' => $this->context,
                )
            );

            return $this->success();
        } catch (\Exception $e) {
            return $this->failure();
        }
    }

    /**
     * Return a label describing this test instance.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label ?: 'WSDL Check';
    }

    /**
     * @param object $circuitBreaker
     * @return Wsdl
     */
    public function setCircuitBreaker($circuitBreaker)
    {
        $circuitBreakerInterface = 'Ejsmont\CircuitBreaker\CircuitBreakerInterface';
        if (false === is_subclass_of($circuitBreaker, $circuitBreakerInterface)) {
            // Only checking if CircuitBreakerInterface exists if the object doesn't implement it
            // This allows Unit Testing
            if (false === interface_exists($circuitBreakerInterface)) {
                throw new \RuntimeException('Circuit Breaker notifying requires ejsmont-artur/php-circuit-breaker');
            }

            $class = get_class($circuitBreaker);
            throw new \RuntimeException("Expected instance of $circuitBreakerInterface but got $class");
        }

        $this->circuitBreaker = $circuitBreaker;

        return $this;
    }

    /**
     * @param string $circuitBreakerServiceId
     * @return Wsdl
     */
    public function setCircuitBreakerServiceId($circuitBreakerServiceId)
    {
        $this->circuitBreakerServiceId = $circuitBreakerServiceId;

        return $this;
    }

    private function success()
    {
        if ($this->circuitBreaker && $this->circuitBreakerServiceId) {
            $this->circuitBreaker->reportSuccess($this->circuitBreakerServiceId);
        }

        return new Success();
    }

    private function failure()
    {
        if ($this->circuitBreaker && $this->circuitBreakerServiceId) {
            $this->circuitBreaker->reportFailure($this->circuitBreakerServiceId);
        }

        return new Failure();
    }
}
