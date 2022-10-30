<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Tests\Security\Role;

use Imatic\Bundle\UserBundle\Security\Role\ObjectRole;
use Imatic\Bundle\UserBundle\Security\Role\ObjectRoleFactory;
use PHPUnit\Framework\TestCase;

class ObjectRoleFactoryTest extends TestCase
{
    private ObjectRoleFactory $roleFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->roleFactory = new ObjectRoleFactory();
    }

    public function testCreateRole(): void
    {
        $role = new ObjectRole('Vendor', 'Foo', 'entity', 'Class', 'property', 'action');
        $this->assertEquals(
            $role,
            $this->roleFactory->createRole('Vendor\Foo\Entity\Class', 'property', 'action')
        );
        $this->assertEquals(
            $role,
            $this->roleFactory->createRole('Vendor\FooBundle\Entity\Class', 'property', 'action')
        );
        $this->assertEquals(
            $role,
            $this->roleFactory->createRole('Vendor\Bundle\FooBundle\Entity\Class', 'property', 'action')
        );
        $this->assertEquals(
            new ObjectRole('Imatic', 'User', 'tests', 'Security_Role_ObjectRoleFactoryTest', 'property', 'action'),
            $this->roleFactory->createRole($this, 'property', 'action')
        );
    }

    public function testCreateRoleShouldThrowException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The class name "class" is missing either a vendor, bundle or a type name.');

        $this->roleFactory->createRole('class', 'name', 'action');
    }
}
