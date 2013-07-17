<?php
namespace Imatic\Bundle\UserBundle\Security\Role;

class HierarchyRoleProvider implements RoleProviderInterface
{
    /** @var string[] */
    private $roleHierarchy;

    /** @var SimpleRole[] */
    private $roles;

    /**
     * @param string[]
     */
    public function __construct(array $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;
    }

    /**
     * @return SimpleRole[]
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
                    $this->roles[$role] = isset($this->roles[$role]) ? $this->roles[$role] : new SimpleRole($role);
                    $children[] = $this->roles[$role];
                }

                $this->roles[$name] = new SimpleRole($name, $children);
            }
        }

        return array_values($this->roles);
    }

    /**
     * @param string $role
     * @param string $property
     * @param string $action
     * @return SimpleRole|null
     */
    public function getRole($role, $property = '', $action = '')
    {
        $this->getRoles();

        if (is_scalar($role) && isset($this->roles[$role])) {
            return $this->roles[$role];
        }

        return null;
    }
}