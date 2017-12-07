<?php
namespace Imatic\Bundle\UserBundle\Security\Role\Provider;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Imatic\Bundle\UserBundle\Security\Role\ConfigAwareInterface;
use Imatic\Bundle\UserBundle\Security\Role\ObjectRole;
use Imatic\Bundle\UserBundle\Security\Role\ObjectRoleFactory;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Config\Definition\Processor;

class ModelRoleProvider implements RoleProviderInterface, ConfigAwareInterface
{
    /** @var ClassMetadataFactory */
    private $metadataFactory;

    /** @var ObjectRoleFactory */
    private $roleFactory;

    /** @var array */
    private $config;

    /** @var string[] */
    private $actions = ['show', 'edit'];

    /** @var ObjectRole[]|null */
    private $roles;

    /** @var array|null */
    private $filters;

    /** @var array|null */
    private $propertyIncludes;

    /** @var array|null */
    private $propertyExcludes;

    /** @var array|null */
    private $propertyGroups;

    /**
     * @param ClassMetadataFactory $metadataFactory
     */
    public function __construct(ClassMetadataFactory $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
        $this->roleFactory = new ObjectRoleFactory();
        $this->setConfig();
    }

    /**
     * @return ObjectRole[]
     */
    public function getRoles()
    {
        if ($this->roles === null) {
            $this->roles = [];

            foreach ($this->getAllMetadata() as $metadata) {
                if (!$this->isClassIncluded($metadata->name)) {
                    continue;
                }

                foreach ($this->getModelProperties($metadata) as $property) {
                    foreach ($this->actions as $action) {
                        $key = $this->getRoleKey($metadata->name, $property, $action);
                        $this->roles[$metadata->name][$key] = $this->roleFactory->createRole(
                            $metadata->name,
                            $property,
                            $action
                        );
                    }
                }
            }
        }

        return $this->roles ? \array_values(\call_user_func_array('array_merge', $this->roles)) : [];
    }

    /**
     * @param object|string $object
     * @param string        $property
     * @param string        $action
     *
     * @return ObjectRole|null
     */
    public function getRole($object, $property, $action)
    {
        $this->getRoles();
        $class = $this->getClass($object);
        $propertyGroups = $this->getPropertyGroups();
        $key = $this->getRoleKey(
            $class,
            isset($propertyGroups[$class][$property]) ? $propertyGroups[$class][$property] : $property,
            $action
        );

        return isset($this->roles[$class][$key]) ? $this->roles[$class][$key] : null;
    }

    /**
     * @param array $config
     *
     * @return $this
     *
     * @throws InvalidConfigurationException
     */
    public function setConfig(array $config = [])
    {
        $this->config = (new Processor())->process($this->getConfigurationTree(), [$config]);
        $this->roles = null;
        $this->filters = null;
        $this->propertyIncludes = null;
        $this->propertyExcludes = null;
        $this->propertyGroups = null;

        return $this;
    }

    /**
     * @param string[] $actions
     *
     * @return $this
     */
    public function setActions(array $actions)
    {
        $this->actions = $actions;
        $this->roles = null;

        return $this;
    }

    /**
     * @return ClassMetadata[]
     */
    private function getAllMetadata()
    {
        return $this->metadataFactory->getAllMetadata();
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    private function isClassIncluded($class)
    {
        foreach ($this->getFilters() as $prefix => $filter) {
            if (!\strncasecmp($class . '\\', $prefix, \strlen($prefix))) {
                return $filter[0] === 'include';
            }
        }

        return false;
    }

    /**
     * @param ClassMetadata $metadata
     *
     * @return string[]
     */
    private function getModelProperties(ClassMetadata $metadata)
    {
        $propertyIncludes = $this->getPropertyIncludes();
        $propertyExcludes = $this->getPropertyExcludes();
        $propertyGroups = $this->getPropertyGroups();
        $properties = isset($propertyIncludes[$metadata->name])
            ? $propertyIncludes[$metadata->name]
            : \array_merge($metadata->getFieldNames(), $metadata->getAssociationNames());

        if (isset($propertyGroups[$metadata->name])) {
            $properties = \array_merge(
                \array_diff($properties, \array_keys($propertyGroups[$metadata->name])),
                $propertyGroups[$metadata->name]
            );
        }

        if (isset($propertyExcludes[$metadata->name])) {
            $properties = \array_diff($properties, $propertyExcludes[$metadata->name]);
        }

        return $properties;
    }

    /**
     * @param string $class
     * @param string $property
     * @param string $action
     *
     * @return string
     */
    private function getRoleKey($class, $property, $action)
    {
        return \sprintf('%s-%s-%s', $class, $property, $action);
    }

    /**
     * @param object|string $object
     *
     * @return string
     */
    private function getClass($object)
    {
        $class = \is_object($object) ? \get_class($object) : $object;

        if (\is_subclass_of($class, 'Doctrine\Common\Proxy\Proxy')) {
            $class = \get_parent_class($class);
        }

        return $class;
    }

    /**
     * @return array
     */
    private function getFilters()
    {
        if ($this->filters === null) {
            $this->filters = [];
            $configuration = [
                'include' => $this->config['namespaces']['includes'],
                'exclude' => $this->config['namespaces']['excludes'],
            ];

            foreach ($configuration as $type => $filter) {
                foreach ($filter as $prefix) {
                    $this->filters[\ltrim(\rtrim($prefix, '\\'), '\\') . '\\'] = [$type, \substr_count($prefix, '\\')];
                }
            }

            \uasort($this->filters, function ($a, $b) {
                return $this->compareFilters($a, $b);
            });
        }

        return $this->filters;
    }

    /**
     * @return array
     */
    private function getPropertyIncludes()
    {
        if ($this->propertyIncludes === null) {
            $this->propertyIncludes = [];

            foreach ($this->config['properties']['includes'] as $class => $property) {
                $this->propertyIncludes[$this->metadataFactory->getMetadataFor($class)->name] = $property;
            }
        }

        return $this->propertyIncludes;
    }

    /**
     * @return array
     */
    private function getPropertyExcludes()
    {
        if ($this->propertyExcludes === null) {
            $this->propertyExcludes = [];

            foreach ($this->config['properties']['excludes'] as $class => $property) {
                $this->propertyExcludes[$this->metadataFactory->getMetadataFor($class)->name] = $property;
            }
        }

        return $this->propertyExcludes;
    }

    /**
     * @return array
     */
    private function getPropertyGroups()
    {
        if ($this->propertyGroups === null) {
            $this->propertyGroups = [];

            foreach ($this->config['properties']['groups'] as $class => $group) {
                $class = $this->metadataFactory->getMetadataFor($class)->name;
                $this->propertyGroups[$class] = [];

                foreach ($group as $name => $properties) {
                    $this->propertyGroups[$class] += \array_fill_keys($properties, $name);
                }
            }
        }

        return $this->propertyGroups;
    }

    /**
     * @param string $a
     * @param string $b
     *
     * @return int
     */
    private function compareFilters($a, $b)
    {
        if ($a[1] === $b[1]) {
            return 0;
        }

        return $a[1] > $b[1] ? -1 : 1;
    }

    /**
     * @return NodeInterface
     */
    private function getConfigurationTree()
    {
        $builder = new TreeBuilder();
        $root = $builder->root('config');
        $root
            ->children()
                ->arrayNode('namespaces')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('includes')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('excludes')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('properties')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('includes')
                            ->prototype('array')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                        ->arrayNode('excludes')
                            ->prototype('array')
                                ->prototype('scalar')->end()
                            ->end()
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
            ->end();

        return $builder->buildTree();
    }
}
