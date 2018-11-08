<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\DependencyInjection;

use Imatic\Bundle\UserBundle\Form\Type\User\UserType;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Marek Stipek <marek.stipek@imatic.cz>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $rootNode = $builder->root('imatic_user');
        $this->addEntitiesSection($rootNode);
        $this->addSecuritySection($rootNode);
        $this->addAdminSection($rootNode);

        return $builder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addEntitiesSection($node): void
    {
        $node
            ->children()
                ->arrayNode('entities')
                    ->children()
                        ->scalarNode('em')
                            ->cannotBeEmpty()
                            ->defaultValue('doctrine.orm.entity_manager')
                            ->info('Entity manager service name')
                            ->example('doctrine.orm.entity_manager')
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

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addAdminSection($node): void
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

    /**
     * @param ArrayNodeDefinition $node
     */
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
