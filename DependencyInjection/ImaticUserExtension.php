<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Marek Stipek <marek.stipek@imatic.cz>
 */
class ImaticUserExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('imatic_user.entity.user.class', $config['entities']['user']);
        $container->setParameter('imatic_user.entity.group.class', $config['entities']['group']);

        $container->setParameter('imatic_user.admin.form.user', $config['admin']['form']['user']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $fosUserConfig = [
            'user_class' => $config['entities']['user'],
            'group' => [
                'group_class' => $config['entities']['group'],
            ],
        ];

        $container->prependExtensionConfig('fos_user', $fosUserConfig);
    }
}
