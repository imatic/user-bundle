<?php
namespace Imatic\Bundle\UserBundle\Tests\Security\Role;

use Imatic\Bundle\UserBundle\Security\Role\HierarchyRoleProvider;
use Imatic\Bundle\UserBundle\Security\Role\Role;

class HierarchyRoleProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var HierarchyRoleProvider */
    private $roleProvider;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->roleProvider = new HierarchyRoleProvider([
            'ROLE_ADMIN' => ['ROLE_USER'],
            'ROLE_SUPER_ADMIN' => ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_ALLOWED_TO_SWITCH']
        ]);
    }

    public function testGetRoles()
    {
        $this->assertEquals([], (new HierarchyRoleProvider([]))->getRoles());
        $this->assertEquals(
            [
                new Role('app', 'global', 'global', 'ROLE_ADMIN'),
                new Role('app', 'global', 'global', 'ROLE_SUPER_ADMIN'),
                new Role('app', 'global', 'global', 'ROLE_USER'),
                new Role('app', 'global', 'global', 'ROLE_ALLOWED_TO_SWITCH')
            ],
            $this->roleProvider->getRoles()
        );
    }

    public function testGetRole()
    {
        $this->assertNull($this->roleProvider->getRole('FOO'));
        $this->assertEquals(
            new Role('app', 'global', 'global', 'ROLE_ADMIN'),
            $this->roleProvider->getRole('ROLE_ADMIN')
        );
    }
}