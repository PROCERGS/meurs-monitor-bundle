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
