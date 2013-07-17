<?php

namespace Imatic\Bundle\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\Config\Resource\FileResource;

/**
 * Registers the additional validators according to the storage.
 *
 * @author Christophe Coevoet <stof@notk.org>
 * @author Viliam Husár <viliam.husar@imatic.cz>
 */
class ValidationPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('fos_user.storage')) {
            return;
        }

        $storage = $container->getParameter('fos_user.storage');
        if ('custom' === $storage) {
            return;
        }

        if (!$container->hasParameter('validator.mapping.loader.xml_files_loader.mapping_files')) {
            return;
        }

        $files = $container->getParameter('validator.mapping.loader.xml_files_loader.mapping_files');
        $validationFile = __DIR__ . '/../../Resources/config/validation/' . $storage . '.xml';

        if (is_file($validationFile)) {
            $files[] = realpath($validationFile);
            $container->addResource(new FileResource($validationFile));
        }

        $container->setParameter('validator.mapping.loader.xml_files_loader.mapping_files', $files);
    }
}
