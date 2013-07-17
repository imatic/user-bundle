<?php
namespace Imatic\Bundle\UserBundle\Tests\Security\Role;

use Imatic\Bundle\UserBundle\Security\Role\HierarchyRoleProvider;
use Imatic\Bundle\UserBundle\Security\Role\SimpleRole;

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
                new SimpleRole('ROLE_ADMIN', [new SimpleRole('ROLE_USER')]),
                new SimpleRole('ROLE_USER'),
                new SimpleRole('ROLE_SUPER_ADMIN', [
                    new SimpleRole('ROLE_USER'),
                    new SimpleRole('ROLE_ADMIN', [new SimpleRole('ROLE_USER')]),
                    new SimpleRole('ROLE_ALLOWED_TO_SWITCH')
                ]),
                new SimpleRole('ROLE_ALLOWED_TO_SWITCH')
            ],
            $this->roleProvider->getRoles()
        );
    }

    public function testGetRole()
    {
        $this->assertNull($this->roleProvider->getRole('FOO'));
        $this->assertEquals(
            new SimpleRole('ROLE_ADMIN', [new SimpleRole('ROLE_USER')]),
            $this->roleProvider->getRole('ROLE_ADMIN')
        );
    }
}