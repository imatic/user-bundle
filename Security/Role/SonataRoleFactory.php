<?php
namespace Imatic\Bundle\UserBundle\Security\Role;

class SonataRoleFactory extends ObjectRoleFactory
{
    /**
     * @param object|string $object
     * @param string $action
     * @param string $role
     * @return SonataRole
     * @throws \InvalidArgumentException
     */
    public function createRole($object, $action, $role)
    {
        $arguments = $this->parseClassName($object);

        return new SonataRole(
            $arguments['vendor'],
            $arguments['bundle'],
            $arguments['type'],
            $arguments['name'],
            $action,
            $role
        );
    }
}