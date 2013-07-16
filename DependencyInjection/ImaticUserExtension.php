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
        $tag = 'imatic_user.role_provider';
        $interface = 'Imatic\Bundle\UserBundle\Security\Role\RoleProviderInterface';

        foreach ($container->findTaggedServiceIds($tag) as $id => $tags) {
            if (!is_subclass_of($container->getDefinition($id)->getClass(), $interface)) {
                throw new \InvalidArgumentException(sprintf(
                    'The service "%s" tagged as "%s" must be an instance of "%s".',
                    $id,
                    $tag,
                    $interface
                ));
            }

            foreach ($tags as $attributes) {
                $alias = isset($attributes['alias']) ? $attributes['alias'] : $id;
                $configurationId = $alias . '.configuration';
                $definition = new DefinitionDecorator('imatic_user.security.configuration.abstract');

                if (isset($configuration[$alias])) {
                    $config = $configuration[$alias];
                    $definition->setArguments([$config['excludes'], $config['includes'], $config['groups']]);
                }

                $container->setDefinition($configurationId, $definition);
                $container->getDefinition($id)->addMethodCall('setConfiguration', [new Reference($configurationId)]);
            }
        }
    }
}
