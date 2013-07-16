<?php

namespace Imatic\Bundle\UserBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author Marek Stipek <marek.stipek@imatic.cz>
 */
class ImaticUserExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->processConfiguration(new Configuration(), $configs);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $this->loadSecurity($configuration['security'], $container);
    }

    /**
     * @param  array                     $configuration
     * @param  ContainerBuilder          $container
     * @throws \InvalidArgumentException
     */
    private function loadSecurity(array $configuration, ContainerBuilder $container)
    {
        $interface = 'Imatic\Bundle\UserBundle\Security\Role\ConfigAwareInterface';
        $definition = $container->getDefinition('imatic_user.security.chain_role_provider');
        $aliases = [];

        foreach ($container->getAliases() as $name => $alias) {
            $aliases[(string) $alias][] = $name;
        }

        foreach (array_keys($container->findTaggedServiceIds('imatic_user.role_provider')) as $id) {
            if (is_subclass_of($container->getDefinition($id)->getClass(), $interface)) {
                $ids = isset($aliases[$id]) ? $aliases[$id] : [];
                $ids[] = $id;

                foreach ($ids as $name) {
                    if (isset($configuration['config'][$name])) {
                        $container->getDefinition($id)->addMethodCall('setConfig', [$configuration['config'][$name]]);
                        break;
                    }
                }
            }
        }

        foreach ($configuration['providers'] as $id) {
            $definition->addMethodCall('addRoleProvider', [new Reference($id)]);
        }
    }
}
