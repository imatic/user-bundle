<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\DependencyInjection;

use Doctrine\ORM\EntityManagerInterface;
use Imatic\Bundle\UserBundle\Form\Type\User\UserType;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Marek Stipek <marek.stipek@imatic.cz>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('imatic_user');
        $rootNode = $builder->getRootNode();
        $this->addEmailsSection($rootNode);
        $this->addEntitiesSection($rootNode);
        $this->addSecuritySection($rootNode);
        $this->addAdminSection($rootNode);

        return $builder;
    }

    public function addEmailsSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('email')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('address')
                            ->cannotBeEmpty()
                            ->defaultValue('from@example.com')
                        ->end()
                        ->scalarNode('sender_name')
                            ->cannotBeEmpty()
                            ->defaultValue('From')
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addEntitiesSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('entities')
                    ->children()
                        ->scalarNode('em')
                            ->cannotBeEmpty()
                            ->defaultValue(EntityManagerInterface::class)
                            ->info('Entity manager service name')
                            ->example(EntityManagerInterface::class)
                        ->end()
                        ->scalarNode('user')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->info('Entity class which implements Imatic\Bundle\UserBundle\Model\UserInterface')
                            ->example('AppUserBundle\Entity\User')
                        ->end()
                        ->scalarNode('group')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->info('Entity class which implements Imatic\Bundle\UserBundle\Model\GroupInterface')
                            ->example('AppUserBundle\Entity\Group')
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addAdminSection(ArrayNodeDefinition $node): void
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('admin')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('form')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('user')
                                ->isRequired()
                                ->cannotBeEmpty()
                                ->info('User form type')
                                ->example(UserType::class)
                                ->defaultValue(UserType::class)
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addSecuritySection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('security')
                    ->canBeDisabled()
                    ->children()
                        ->arrayNode('role')
                            ->prototype('array')
                                ->prototype('variable')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
