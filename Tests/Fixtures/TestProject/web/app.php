<?php declare(strict_types=1);

require __DIR__ . '/../../../bootstrap.php';
\umask(0007);

use Imatic\Bundle\UserBundle\Tests\Fixtures\TestProject\TestKernel;
use Symfony\Component\HttpFoundation\Request;

$_SERVER['PHP_AUTH_USER'] = 'adam';
$_SERVER['PHP_AUTH_PW'] = '1234';

$kernel = new TestKernel();
$kernel->loadClassCache();

$request = Request::createFromGlobals();
Request::enableHttpMethodParameterOverride();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
