<?php
namespace Imatic\Bundle\UserBundle\Security\Role;

interface RoleProviderInterface
{
    /**
     * @return Role[]
     */
    public function getRoles();

    /**
     * @param mixed $object
     * @param string $property
     * @param string $action
     * @return Role|null
     */
    public function getRole($object, $property = '', $action = '');
}