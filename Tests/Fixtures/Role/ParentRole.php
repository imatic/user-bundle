<?php
namespace Imatic\Bundle\UserBundle\Tests\Fixtures\Role;

use Imatic\Bundle\UserBundle\Security\Role\Role;

class ParentRole extends Role
{
    /** @var string */
    private $role;

    /**
     * @param string $role
     */
    public function __construct($role)
    {
        $this->role = (string) $role;
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
        return 'type';
    }
}