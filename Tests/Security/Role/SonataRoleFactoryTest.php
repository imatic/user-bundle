<?php
namespace Imatic\Bundle\UserBundle\Tests\Security\Role;

use Imatic\Bundle\UserBundle\Security\Role\SonataRoleFactory;
use Imatic\Bundle\UserBundle\Security\Role\SonataRole;

class SonataRoleFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var SonataRoleFactory */
    private $roleFactory;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->roleFactory = new SonataRoleFactory();
    }

    public function testCreateRole()
    {
        $this->assertEquals(
            new SonataRole('Vendor', 'Foo', 'admin', 'Class', 'action', 'ROLE'),
            $this->roleFactory->createRole('Vendor\Foo\Admin\Class', 'action', 'ROLE')
        );
        $this->assertEquals(
            new SonataRole('Imatic', 'User', 'tests', 'Security_Role_SonataRoleFactoryTest', 'action', 'ROLE'),
            $this->roleFactory->createRole($this, 'action', 'ROLE')
        );
    }
}