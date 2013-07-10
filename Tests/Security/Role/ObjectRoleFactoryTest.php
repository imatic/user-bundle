<?php
namespace Imatic\Bundle\UserBundle\Tests\Security\Role;

use Imatic\Bundle\UserBundle\Security\Role\ObjectRoleFactory;
use Imatic\Bundle\UserBundle\Security\Role\Role;

class ObjectRoleFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjectRoleFactory */
    private $roleFactory;

    protected function setUp()
    {
        $this->roleFactory = new ObjectRoleFactory();
    }

    public function testCreateRole()
    {
        $role = new Role('Vendor', 'Foo', 'entity', 'Class', 'action', 'property');
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
            new Role('Imatic', 'User', 'tests', 'Security_Role_ObjectRoleFactoryTest', 'action', 'property'),
            $this->roleFactory->createRole($this, 'property', 'action')
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The class name "class" is missing either a vendor, bundle or a type name.
     */
    public function testCreateRoleShouldThrowException()
    {
        $this->roleFactory->createRole('class', 'name', 'action');
    }
}