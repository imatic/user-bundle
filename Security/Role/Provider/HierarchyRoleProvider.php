<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Security\Role\Provider;

use Imatic\Bundle\UserBundle\Security\Role\HierarchyRole;

class HierarchyRoleProvider implements RoleProviderInterface
{
    /** @var HierarchyRole[] */
    private array $roles;

    /**
     * @param array $roleHierarchy string[]
     */
    public function __construct(
        private array $roleHierarchy
    )
    {
    }

    /**
     * @return HierarchyRole[]
     */
    public function getRoles(): array
    {
        if (empty($this->roles)) {
            if (!$this->roleHierarchy) {
                return [];
            }

            foreach ($this->roleHierarchy as $name => $roles) {
                $children = [];
                $this->roles[$name] = null;

                foreach ($roles as $role) {
                    $this->roles[$role] = isset($this->roles[$role]) ? $this->roles[$role] : new HierarchyRole($role);
                    $children[] = $this->roles[$role];
                }

                $this->roles[$name] = new HierarchyRole($name, $children);
            }
        }

        return $this->roles;
    }
}
