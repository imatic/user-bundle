<?php
namespace Imatic\Bundle\UserBundle\Tests\Security\Role\Provider;

use Imatic\Bundle\UserBundle\Security\Role\HierarchyRole;
use Imatic\Bundle\UserBundle\Security\Role\Provider\HierarchyRoleProvider;

class HierarchyRoleProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var HierarchyRoleProvider */
    private $roleProvider;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->roleProvider = new HierarchyRoleProvider([
            'ROLE_ADMIN' => ['ROLE_USER'],
            'ROLE_SUPER_ADMIN' => ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_ALLOWED_TO_SWITCH'],
        ]);
    }

    public function testGetRoles()
    {
        $this->assertEquals([], (new HierarchyRoleProvider([]))->getRoles());
        $this->assertEquals(
            [
                new HierarchyRole('ROLE_ADMIN', [new HierarchyRole('ROLE_USER')]),
                new HierarchyRole('ROLE_USER'),
                new HierarchyRole('ROLE_SUPER_ADMIN', [
                    new HierarchyRole('ROLE_USER'),
                    new HierarchyRole('ROLE_ADMIN', [new HierarchyRole('ROLE_USER')]),
                    new HierarchyRole('ROLE_ALLOWED_TO_SWITCH'),
                ]),
                new HierarchyRole('ROLE_ALLOWED_TO_SWITCH'),
            ],
            \array_values($this->roleProvider->getRoles())
        );
    }
}
