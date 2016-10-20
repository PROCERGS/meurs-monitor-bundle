<?php

require_once __DIR__.'/../vendor/autoload.php';

use Composer\Autoload\ClassLoader;

$loader = new ClassLoader();
$loader->add('PROCERGS\LoginCidadao\MonitorBundle', __DIR__);
$loader->register();

return $loader;
