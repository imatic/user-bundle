<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Security\Role;

use Symfony\Component\Security\Core\Role\Role as BaseRole;

abstract class Role extends BaseRole
{
    /**
     * @return string
     */
    abstract public function getType();

    /**
     * @return string
     */
    public function getAction()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->getRole();
    }

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
    public function __toString(): string
    {
        return $this->getRole();
    }
}
