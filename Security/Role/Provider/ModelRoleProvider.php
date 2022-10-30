<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Security\Role\Provider;

use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\ClassMetadataFactory;
use Imatic\Bundle\UserBundle\Security\Role\ConfigAwareInterface;
use Imatic\Bundle\UserBundle\Security\Role\ObjectRole;
use Imatic\Bundle\UserBundle\Security\Role\ObjectRoleFactory;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Config\Definition\Processor;

class ModelRoleProvider implements RoleProviderInterface, ConfigAwareInterface
{
    private ObjectRoleFactory $roleFactory;

    private array $config;

    /** @var string[] */
    private array $actions = ['show', 'edit'];

    /** @var ObjectRole[]|null */
    private ?array $roles;

    private ?array $filters;

    private ?array $propertyIncludes;

    private ?array $propertyExcludes;

    private ?array $propertyGroups;

    public function __construct(
        private ClassMetadataFactory $metadataFactory
    )
    {
        $this->roleFactory = new ObjectRoleFactory();
        $this->setConfig();
    }

    /**
     * @return ObjectRole[]
     */
    public function getRoles(): array
    {
        if ($this->roles === null) {
            $this->roles = [];

            foreach ($this->getAllMetadata() as $metadata) {
                if (isset($metadata->name) && !$this->isClassIncluded($metadata->name)) {
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

        return $this->roles ? \array_values(\call_user_func_array('array_merge', \array_values($this->roles))) : [];
    }

    public function getRole(
        object|string $object,
        string $property,
        string $action
    ): ?ObjectRole
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
     * @throws InvalidConfigurationException
     */
    public function setConfig(array $config = []): self
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
     */
    public function setActions(array $actions): self
    {
        $this->actions = $actions;
        $this->roles = null;

        return $this;
    }

    /**
     * @return ClassMetadata[]
     */
    private function getAllMetadata(): array
    {
        return $this->metadataFactory->getAllMetadata();
    }

    private function isClassIncluded(string $class): bool
    {
        foreach ($this->getFilters() as $prefix => $filter) {
            if (!\strncasecmp($class . '\\', $prefix, \strlen($prefix))) {
                return $filter[0] === 'include';
            }
        }

        return false;
    }

    /**
     * @return string[]
     */
    private function getModelProperties(ClassMetadata $metadata): array
    {
        $propertyIncludes = $this->getPropertyIncludes();
        $propertyExcludes = $this->getPropertyExcludes();
        $propertyGroups = $this->getPropertyGroups();
        $properties = (isset($metadata->name) && isset($propertyIncludes[$metadata->name]))
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

    private function getRoleKey(string $class, string $property, string $action): string
    {
        return \sprintf('%s-%s-%s', $class, $property, $action);
    }

    private function getClass(object|string $object): string
    {
        $class = \is_object($object) ? \get_class($object) : $object;

        if (\is_subclass_of($class, 'Doctrine\Common\Proxy\Proxy')) {
            $class = \get_parent_class($class);
        }

        return $class;
    }

    private function getFilters(): array
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

    private function getPropertyIncludes(): array
    {
        if ($this->propertyIncludes === null) {
            $this->propertyIncludes = [];

            foreach ($this->config['properties']['includes'] as $class => $property) {
                $this->propertyIncludes[$this->metadataFactory->getMetadataFor($class)->name] = $property;
            }
        }

        return $this->propertyIncludes;
    }

    private function getPropertyExcludes(): array
    {
        if ($this->propertyExcludes === null) {
            $this->propertyExcludes = [];

            foreach ($this->config['properties']['excludes'] as $class => $property) {
                $this->propertyExcludes[$this->metadataFactory->getMetadataFor($class)->name] = $property;
            }
        }

        return $this->propertyExcludes;
    }

    private function getPropertyGroups(): array
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

    private function compareFilters(string $a, string $b): int
    {
        if ($a[1] === $b[1]) {
            return 0;
        }

        return $a[1] > $b[1] ? -1 : 1;
    }

    private function getConfigurationTree(): NodeInterface
    {
        $builder = new TreeBuilder('config');
        $root = $builder->getRootNode();
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
