<?php
namespace Imatic\Bundle\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SecurityCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->processRoleProviders($container);
        $this->processTranslationStrategies($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function processRoleProviders(ContainerBuilder $container)
    {
        $config = $container->getExtensionConfig('imatic_user');
        $configuration = isset($config[0]['security']['role']) ? $config[0]['security']['role'] : [];
        $interface = 'Imatic\Bundle\UserBundle\Security\Role\ConfigAwareInterface';
        $definition = $container->getDefinition('imatic_user.security.role.provider.chain_role_provider');
        $aliases = [];

        foreach ($container->getAliases() as $name => $alias) {
            $aliases[(string) $alias][] = $name;
        }

        foreach (array_keys($container->findTaggedServiceIds('imatic_user.role_provider')) as $id) {
            $ids = isset($aliases[$id]) ? $aliases[$id] : [];
            $ids[] = $id;

            foreach ($ids as $name) {
                if (array_key_exists($name, $configuration)) {
                    $definition->addMethodCall('addRoleProvider', [new Reference($id)]);

                    if (is_subclass_of($container->getDefinition($id)->getClass(), $interface)) {
                        $container->getDefinition($id)->addMethodCall('setConfig', [$configuration[$name]]);
                        break;
                    }
                }
            }

        }
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function processTranslationStrategies(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('imatic_user.security.role.translation.role_translator');

        foreach (array_keys($container->findTaggedServiceIds('imatic_user.role_translation_strategy')) as $id) {
            $definition->addMethodCall('addStrategy', [new Reference($id)]);
        }
    }
}