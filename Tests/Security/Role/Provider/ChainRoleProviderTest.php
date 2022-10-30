<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Tests\Security\Role\Provider;

use Imatic\Bundle\UserBundle\Security\Role\HierarchyRole;
use Imatic\Bundle\UserBundle\Security\Role\Provider\ChainRoleProvider;
use Imatic\Bundle\UserBundle\Security\Role\Provider\RoleProviderInterface;
use Imatic\Bundle\UserBundle\Security\Role\Role;
use PHPUnit\Framework\TestCase;

class ChainRoleProviderTest extends TestCase
{
    private ChainRoleProvider $roleProvider;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
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

    public function testGetRoles(): void
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
     */
    private function createRoleProviderMock(array $roles): PHPUnit\Framework\MockObject\MockObject
    {
        $roleProviderMock = $this->createMock(RoleProviderInterface::class);
        $roleProviderMock
            ->expects($this->any())
            ->method('getRoles')
            ->will($this->returnValue($roles));

        return $roleProviderMock;
    }
}
