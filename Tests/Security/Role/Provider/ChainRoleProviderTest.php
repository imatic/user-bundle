<?php
namespace Imatic\Bundle\UserBundle\Tests\Security\Role\Provider;

use Imatic\Bundle\UserBundle\Security\Role\HierarchyRole;
use Imatic\Bundle\UserBundle\Security\Role\Provider\ChainRoleProvider;
use Imatic\Bundle\UserBundle\Security\Role\Provider\RoleProviderInterface;
use Imatic\Bundle\UserBundle\Security\Role\Role;

class ChainRoleProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var ChainRoleProvider */
    private $roleProvider;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->roleProvider = new ChainRoleProvider([
            $this->createRoleProviderMock([
                new HierarchyRole('role_a'),
                new HierarchyRole('role_b'),
            ]),
            $this->createRoleProviderMock([
                new HierarchyRole('role_c'),
                new HierarchyRole('role_d'),
            ]),
        ]);
    }

    public function testGetRoles()
    {
        $this->assertEquals(
            [
                new HierarchyRole('role_a'),
                new HierarchyRole('role_b'),
                new HierarchyRole('role_c'),
                new HierarchyRole('role_d'),
            ],
            $this->roleProvider->getRoles()
        );
    }

    /**
     * @param Role[] $roles
     *
     * @return RoleProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createRoleProviderMock(array $roles)
    {
        $roleProviderMock = $this->createMock('Imatic\Bundle\UserBundle\Security\Role\Provider\RoleProviderInterface');
        $roleProviderMock
            ->expects($this->any())
            ->method('getRoles')
            ->will($this->returnValue($roles));

        return $roleProviderMock;
    }
}
