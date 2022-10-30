<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Tests\Fixtures\TestProject;

use Imatic\Bundle\TestingBundle\Test\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }
}
