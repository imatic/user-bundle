<?php
namespace Imatic\Bundle\UserBundle;

use Imatic\Bundle\UserBundle\DependencyInjection\Compiler\SecurityCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ImaticUserBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'FOSUserBundle';
    }

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SecurityCompilerPass());
    }
}