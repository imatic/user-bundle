<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle;

use Imatic\Bundle\UserBundle\DependencyInjection\Compiler\SecurityPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ImaticUserBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new SecurityPass());
    }
}
