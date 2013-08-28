<?php
namespace Imatic\Bundle\UserBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
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
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('imatic_user.entity.user.class', $config['entity']['user']);
        $container->setParameter('imatic_user.entity.group.class', $config['entity']['group']);

        if ($config['admin']) {
//            $container->setParameter('')
        }

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}
