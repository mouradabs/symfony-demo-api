<?php

// This is the front controller used when executing the application in the
// development environment ('dev'). See
//
//   * http://symfony.com/doc/current/cookbook/configuration/front_controllers_and_kernel.html
//   * http://symfony.com/doc/current/cookbook/configuration/environments.html

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

// If you don't want to setup permissions the proper way, just uncomment the
// following PHP line. See:
// http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
//umask(0000);

// This check prevents access to debug front controllers that are deployed by
// accident to production servers. Feel free to remove this, extend it, or make
// something more sophisticated.

/**
 * @var Composer\Autoload\ClassLoader $loader
 */
$loader = require __DIR__.'/../app/autoload.php';
Debug::enable();

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
