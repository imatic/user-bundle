<?php
namespace Imatic\Bundle\UserBundle\Security\Role;

class HierarchyRole extends Role
{
    /** @var string */
    private $role;

    /** @var string */
    private $type;

    /** @var Role[] */
    private $children;

    /**
     * @param string $role
     * @param Role[] $children
     * @param string $type
     */
    public function __construct($role, array $children = [], $type = 'global')
    {
        $this->role = (string) $role;
        $this->type = (string) $type;
        $this->children = $children;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return Role[]
     */
    public function getChildren()
    {
        return $this->children;
    }
}
