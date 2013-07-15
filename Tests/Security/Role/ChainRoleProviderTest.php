<?php
namespace Imatic\Bundle\UserBundle\Tests\Security\Role;

use Imatic\Bundle\UserBundle\Security\Role\ChainRoleProvider;
use Imatic\Bundle\UserBundle\Security\Role\RoleProviderInterface;
use Imatic\Bundle\UserBundle\Security\Role\SimpleRole;

class ChainRoleProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var ChainRoleProvider */
    private $roleProvider;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->roleProvider = new ChainRoleProvider([
            $this->createRoleProviderMock([
                new SimpleRole('role_a'),
                new SimpleRole('role_b')
            ]),
            $this->createRoleProviderMock([
                new SimpleRole('role_c'),
                new SimpleRole('role_d')
            ])
        ]);
    }

    public function testGetRoles()
    {
        $this->assertEquals(
            [
                new SimpleRole('role_a'),
                new SimpleRole('role_b'),
                new SimpleRole('role_c'),
                new SimpleRole('role_d')
            ],
            $this->roleProvider->getRoles()
        );
    }

    public function testGetRole()
    {
        $this->assertEquals('test', $this->roleProvider->getRole('test'));
    }

    /**
     * @param Role[] $roles
     * @return RoleProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createRoleProviderMock(array $roles)
    {
        $roleProviderMock = $this->getMock('Imatic\Bundle\UserBundle\Security\Role\RoleProviderInterface');
        $roleProviderMock
            ->expects($this->any())
            ->method('getRoles')
            ->will($this->returnValue($roles))
        ;
        $roleProviderMock
            ->expects($this->any())
            ->method('getRole')
            ->will($this->returnArgument(0))
        ;

        return $roleProviderMock;
    }
}