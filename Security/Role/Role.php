<?php
namespace Imatic\Bundle\UserBundle\Security\Role;

use Symfony\Component\Security\Core\Role\RoleInterface;

abstract class Role implements RoleInterface
{
    /**
     * @return string
     */
    abstract public function getType();

    /**
     * @return string
     */
    public function getDomain()
    {
        return 'roles';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getRole();
    }
}