<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Security\Role;

class HierarchyRole extends Role
{
    /**
     * @param Role[] $children
     */
    public function __construct(
        private string $role,
        private array $children = [],
        private string $type = 'global'
    ) {
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return Role[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }
}
