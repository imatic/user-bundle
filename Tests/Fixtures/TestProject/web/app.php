<?php
require __DIR__ . '/../../../bootstrap.php';
umask(0007);

use Symfony\Component\HttpFoundation\Request;
use Imatic\Bundle\UserBundle\Tests\Fixtures\TestProject\TestKernel;

$_SERVER['PHP_AUTH_USER'] = 'adam';
$_SERVER['PHP_AUTH_PW'] = '1234';

$kernel = new TestKernel();
$kernel->loadClassCache();

$request = Request::createFromGlobals();
Request::enableHttpMethodParameterOverride();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
