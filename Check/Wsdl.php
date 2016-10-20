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
            set_error_handler(function() { });
            new \SoapClient(
                $this->wsdlUrl,
                array(
                    'cache_wsdl' => WSDL_CACHE_BOTH,
                    'trace' => true,
                    'stream_context' => $this->context,
                )
            );
            restore_error_handler();

            return new Success();
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
        return $this->label ? : 'WSDL Check';
    }
}
