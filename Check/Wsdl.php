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

    /**
     * Wsdl constructor.
     * @param $wsdlUrl
     * @param null $label
     * @param bool $verifyHttps
     */
    public function __construct($wsdlUrl, $label = null, $verifyHttps = true)
    {
        $this->label = $label;
        $this->wsdlUrl = $wsdlUrl;

        if (false === $verifyHttps) {
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
            if (function_exists('xdebug_disable')) {
                xdebug_disable();
            }
            @new \SoapClient(
                $this->wsdlUrl,
                array(
                    'cache_wsdl' => WSDL_CACHE_NONE,
                    'trace' => true,
                    'exceptions' => true,
                    'stream_context' => $this->context,
                )
            );
            if (function_exists('xdebug_enable')) {
                xdebug_enable();
            }

            return $this->success();
        } catch (\SoapFault $e) {
            $error = error_get_last();
            if ($error !== null && $error['message'] == $e->getMessage()) {
                // Overwrites E_ERROR with E_USER_NOTICE as seen in http://stackoverflow.com/a/36667322
                // Added @ to suppress the error
                @trigger_error($e->getMessage());
            }

            return $this->failure();
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

    private function success()
    {
        return new Success();
    }

    private function failure()
    {
        return new Failure();
    }
}
