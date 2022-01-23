<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\DependencyInjection\Compiler;

use Imatic\Bundle\UserBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SecurityPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container): void
    {
        $this->processRoleProviders($container);
        $this->processTranslationStrategies($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function processRoleProviders(ContainerBuilder $container): void
    {
        $config = $container->getExtensionConfig('imatic_user');
        $processor = new Processor();
        $configuration = $processor->processConfiguration(new Configuration(), $config);
        $definition = $container->getDefinition(\Imatic\Bundle\UserBundle\Security\Role\Provider\ChainRoleProvider::class);
        $roleProviders = $configuration['security']['role'];

        foreach ($container->findTaggedServiceIds('imatic_user.role_provider') as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $alias = $attributes['alias'];
                if (!isset($roleProviders[$alias])) {
                    continue;
                }

                $definition->addMethodCall('addRoleProvider', [new Reference($id)]);
                if (\is_subclass_of(
                    $container->getDefinition($id)->getClass(),
                    'Imatic\Bundle\UserBundle\Security\Role\ConfigAwareInterface'
                )
                ) {
                    $container->getDefinition($id)->addMethodCall('setConfig', [$roleProviders[$alias]]);
                    break;
                }
            }
        }
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function processTranslationStrategies(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition(\Imatic\Bundle\UserBundle\Security\Role\Translation\RoleTranslator::class);

        foreach (\array_keys($container->findTaggedServiceIds('imatic_user.role_translation_strategy')) as $id) {
            $definition->addMethodCall('addStrategy', [new Reference($id)]);
        }
    }
}
  
