<?php
namespace Imatic\Bundle\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /** @var \Closure */
    private $arrayValue;

    public function __construct()
    {
        $this->arrayValue = function($value) {
            return [$value];
        };
    }

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
                    ->prototype('array')
                        ->children()
                            ->arrayNode('excludes')
                                ->beforeNormalization()
                                    ->ifString()
                                    ->then($this->arrayValue)
                                ->end()
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('includes')
                                ->beforeNormalization()
                                    ->ifString()
                                    ->then($this->arrayValue)
                                ->end()
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('groups')
                                ->prototype('array')
                                    ->prototype('array')
                                        ->prototype('scalar')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}