<?php
namespace Imatic\Bundle\UserBundle\Security\Role;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;

class ModelRoleProvider implements RoleProviderInterface, ConfigAwareInterface
{
    const CONFIG_INCLUDES = 'includes';
    const CONFIG_EXCLUDES = 'excludes';
    const CONFIG_GROUPS = 'groups';

    /** @var ClassMetadataFactory */
    private $metadataFactory;

    /** @var ObjectRoleFactory */
    private $roleFactory;

    /** @var array */
    private $config = [
        self::CONFIG_INCLUDES => [],
        self::CONFIG_EXCLUDES => [],
        self::CONFIG_GROUPS => []
    ];

    /** @var string[] */
    private $actions = ['show', 'edit'];

    /** @var Role[]|null */
    private $roles;

    /** @var array|null */
    private $groups;

    /** @var array|null */
    private $properties;

    /**
     * @param ClassMetadataFactory $metadataFactory
     */
    public function __construct(ClassMetadataFactory $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
        $this->roleFactory = new ObjectRoleFactory();
    }

    /**
     * @return Role[]
     */
    public function getRoles()
    {
        if ($this->roles === null) {
            $this->roles = [];

            foreach ($this->getAllMetadata() as $metadata) {
                if (!$this->isIncluded($metadata->name)) {
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

        return $this->roles ? array_values(call_user_func_array('array_merge', $this->roles)) : [];
    }

    /**
     * @param mixed $object
     * @param string $property
     * @param string $action
     * @return Role|null
     */
    public function getRole($object, $property = '', $action = '')
    {
        $this->getRoles();
        $class = $this->getClass($object);
        $properties = $this->getProperties();
        $key = $this->getRoleKey(
            $class,
            isset($properties[$class][$property]) ? $properties[$class][$property] : $property,
            $action
        );

        return isset($this->roles[$class][$key]) ? $this->roles[$class][$key] : null;
    }

    /**
     * @param array $config
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setConfig(array $config)
    {
        foreach ($config as $name => $value) {
            if (!isset($this->config[$name])) {
                throw new \InvalidArgumentException(sprintf('The config name "%s" is not valid.', $name));
            }

            $this->config[$name] = (array) $value;
        }

        $this->roles = null;
        $this->groups = null;

        return $this;
    }

    /**
     * @param string[] $actions
     * @return $this
     */
    public function setActions(array $actions)
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * @param string $class
     * @return bool
     */
    public function isIncluded($class)
    {
        foreach ($this->getFilters() as $prefix => $filter) {
            if (!strncasecmp($class . '\\', $prefix, strlen($prefix))) {
                return $filter[0] == 'include';
            }
        }

        return false;
    }

    /**
     * @return ClassMetadata[]
     */
    private function getAllMetadata()
    {
        return $this->metadataFactory->getAllMetadata();
    }

    /**
     * @param ClassMetadata $metadata
     * @return string[]
     */
    private function getModelProperties(ClassMetadata $metadata)
    {
        $groups = $this->getGroups();
        $properties = array_merge($metadata->getFieldNames(), $metadata->getAssociationNames());

        if (isset($groups[$metadata->name])) {
            $properties = array_diff($properties, call_user_func_array('array_merge', $groups[$metadata->name]));
            $properties = array_merge($properties, array_keys($groups[$metadata->name]));
        }

        return $properties;
    }

    /**
     * @param object|string $object
     * @return string
     */
    private function getClass($object)
    {
        $class = is_object($object) ? get_class($object) : $object;

        if (is_subclass_of($class, 'Doctrine\Common\Proxy\Proxy')) {
            $class = get_parent_class($class);
        }

        return $class;
    }

    /**
     * @param string $class
     * @param string $property
     * @param string $action
     * @return string
     */
    private function getRoleKey($class, $property, $action)
    {
        return sprintf('%s-%s-%s', $class, $property, $action);
    }

    /**
     * @return array
     */
    private function getGroups()
    {
        if ($this->groups === null) {
            $this->groups = [];

            foreach ($this->config[static::CONFIG_GROUPS] as $class => $group) {
                $this->groups[$this->metadataFactory->getMetadataFor($class)->name] = $group;
            }
        }

        return $this->groups;
    }

    /**
     * @return array
     */
    private function getProperties()
    {
        if ($this->properties === null) {
            $this->properties = [];

            foreach ($this->getGroups() as $class => $groups) {
                $this->properties[$class] = [];

                foreach ($groups as $name => $properties) {
                    $this->properties[$class] += array_fill_keys($properties, $name);
                }
            }
        }

        return $this->properties;
    }

    /**
     * @return array
     */
    private function getFilters()
    {
        $configuration = [
            'include' => $this->config[static::CONFIG_INCLUDES],
            'exclude' => $this->config[static::CONFIG_EXCLUDES]
        ];
        $filters = [];

        foreach ($configuration as $type => $filter) {
            foreach ($filter as $prefix) {
                $filters[ltrim(rtrim($prefix, '\\'), '\\') . '\\'] = [$type, substr_count($prefix, '\\')];
            }
        }

        uasort($filters, [$this, 'sortFilters']);

        return $filters;
    }

    /**
     * @param string $a
     * @param string $b
     * @return int
     */
    private function sortFilters($a, $b)
    {
        if ($a[1] == $b[1]) {
            return 0;
        }

        return $a[1] > $b[1] ? -1 : 1;
    }
}