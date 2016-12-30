<?php

namespace Imatic\Bundle\UserBundle\Security\Role;

class ObjectRoleFactory
{
    /**
     * @param object|string $object
     * @param string        $property
     * @param string        $action
     *
     * @return ObjectRole
     *
     * @throws \InvalidArgumentException
     */
    public function createRole($object, $property, $action)
    {
        $arguments = $this->parseClassName($object);

        return new ObjectRole(
            $arguments['vendor'],
            $arguments['bundle'],
            $arguments['type'],
            $arguments['name'],
            $property,
            $action
        );
    }

    /**
     * @param $object
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function parseClassName($object)
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

        return [
            'vendor' => $vendor,
            'bundle' => $bundle,
            'type' => $type,
            'name' => implode('_', $name),
        ];
    }
}
