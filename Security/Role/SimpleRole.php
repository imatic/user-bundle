<?php
namespace Imatic\Bundle\UserBundle\Security\Role;

class SimpleRole extends Role
{
    /** @var string */
    private $role;

    /** @var string */
    private $type;

    /**
     * @param string $role
     */
    public function __construct($role, $type = 'global')
    {
        $this->role = (string) $role;
        $this->type = (string) $type;
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
}