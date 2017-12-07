<?php
namespace Imatic\Bundle\UserBundle\Security\Role\Provider;

use Imatic\Bundle\UserBundle\Security\Role\HierarchyRole;

class HierarchyRoleProvider implements RoleProviderInterface
{
    /** @var string[] */
    private $roleHierarchy;

    /** @var HierarchyRole[] */
    private $roles;

    /**
     * @param string[]
     */
    public function __construct(array $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;
    }

    /**
     * @return HierarchyRole[]
     */
    public function getRoles()
    {
        if ($this->roles === null) {
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
