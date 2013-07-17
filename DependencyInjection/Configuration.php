<?php
namespace Imatic\Bundle\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $root = $builder->root('imatic_user');
        $this->addSecuritySection($root);

        return $builder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addSecuritySection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('security')
                    ->canBeDisabled()
                    ->children()
                        ->arrayNode('providers')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('config')
                            ->prototype('array')
                                ->prototype('variable')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}