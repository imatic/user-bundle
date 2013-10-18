<?php
namespace Imatic\Bundle\UserBundle;

use Imatic\Bundle\PlatformBundle\DependencyInjection\Compiler\ResolveTargetEntityPass;
use Imatic\Bundle\UserBundle\DependencyInjection\Compiler\SecurityPass;
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
        $container->addCompilerPass(new SecurityPass());
//        $container->addCompilerPass(
//            new ResolveTargetEntityPass('Imatic/Bundle/UserBundle/Model/UserInterface', '%imatic_user.entity.user.class%')
//        );
//        $container->addCompilerPass(
//            new ResolveTargetEntityPass('Imatic/Bundle/UserBundle/Model/GroupInterface', '%imatic_user.entity.group.class%')
//        );
    }
}
