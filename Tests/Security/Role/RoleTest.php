<?php
namespace Imatic\Bundle\UserBundle\Tests\Security\Role;

use Imatic\Bundle\UserBundle\Security\Role\Role;

class RoleTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRole()
    {
        $this->assertEquals(
            'ROLE_VENDOR_BUNDLE_TYPE_NAME_ACTION',
            (new Role('vendor', 'bundle', 'type', 'name', 'action'))->getRole()
        );
    }
}