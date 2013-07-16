<?php
namespace Imatic\Bundle\UserBundle\Security\Role;

interface RoleProviderInterface
{
    /**
     * @param string|null $class
     *
     * @return Role[]
     */
    public function getRoles($class = null);

    /**
     * @param object|string $object
     * @param string        $property
     * @param string        $action
     *
     * @return Role
     */
    public function getRole($object, $property, $action);

    /**
     * @param Configuration $configuration
     *
     * @return $this
     */
    public function setConfiguration(Configuration $configuration);
}
