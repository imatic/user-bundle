<?php
namespace Imatic\Bundle\UserBundle\Security\Role;

class HierarchyRoleProvider implements RoleProviderInterface
{
    /** @var string[] */
    private $roleHierarchy;

    private $roleFactory;

    /** @var Role[] */
    private $roles;

    /**
     * @param string[]
     */
    public function __construct(array $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;
        $this->roleFactory = new ObjectRoleFactory();
    }

    /**
     * @return Role[]
     */
    public function getRoles()
    {
        if ($this->roles === null) {
            if (!$this->roleHierarchy) {
                return [];
            }

            $roles = array_merge(
                array_keys($this->roleHierarchy),
                call_user_func_array('array_merge', $this->roleHierarchy)
            );

            foreach ($roles as $role) {
                $this->roles[$role] = new Role('', '', 'global', 'roles', $role);
            }
        }

        return array_values($this->roles);
    }

    /**
     * @param string $role
     * @return Role|null
     */
    public function getRole($role, $_ = '', $_ = '')
    {
        $this->getRoles();

        if (is_scalar($role) && isset($this->roles[$role])) {
            return $this->roles[$role];
        }

        return null;
    }
}