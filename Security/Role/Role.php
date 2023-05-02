<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Security\Role;

use Symfony\Component\Security\Core\Role\Role as BaseRole;

abstract class Role extends BaseRole
{
    abstract public function getType(): string;

    public function getAction(): string
    {
        return '';
    }

    public function getLabel(): string
    {
        return $this->getRole();
    }

    public function getDomain(): string
    {
        return 'roles';
    }

    public function __toString(): string
    {
        return $this->getRole();
    }
}
