<?php

namespace Imatic\Bundle\UserBundle\Security\Role;

use InvalidArgumentException;

/**
 * Class ObjectRoleFactory
 * @package Imatic\Bundle\UserBundle\Security\Role
 */
class ObjectRoleFactory
{
    /**
     * @param object|string $object
     * @param string        $property
     * @param string        $action
     *
     * @return Role
     *
     * @throws \InvalidArgumentException
     */
    public function createRole($object, $property, $action)
    {
        $class = is_object($object) ? get_class($object) : $object;
        $path = explode('\\', $class);
        $vendor = $path[0];

        if (count($path) < 4) {
            throw new \InvalidArgumentException(sprintf(
                'The class name "%s" is missing either a vendor, bundle or a type name.',
                $class
            ));
        }

        $name = array_slice($path, $path[1] == 'Bundle' ? 2 : 1);
        $bundle = array_shift($name);
        $type = strtolower(array_shift($name));

        if (substr($bundle, -6) == 'Bundle') {
            $bundle = substr($bundle, 0, -6);
        }

        return new Role($vendor, $bundle, $type, implode('_', $name), $action, $property);
    }
}
