<?php
namespace Imatic\Bundle\UserBundle\Tests\Fixtures\TestProject\ImaticUserBundle\Security\Role;

use Imatic\Bundle\UserBundle\Security\Role\Role as BaseRole;

/**
 * @author Miloslav Nenadal <miloslav.nenadal@imatic.cz>
 */
class Role extends BaseRole
{
    protected $role;

    public function __construct($name)
    {
        $this->role = $name;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function getType()
    {
        return 'dumm';
    }
}
