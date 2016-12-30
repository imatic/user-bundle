<?php

namespace Imatic\Bundle\UserBundle\Tests\Security\Role;

use Imatic\Bundle\UserBundle\Security\Role\ObjectRole;

class ObjectRoleTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjectRole */
    private $role;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->role = new ObjectRole('Vendor', 'Bundle', 'type', 'Name', 'property', 'action');
    }

    public function testGetDomain()
    {
        $this->assertEquals('VendorBundleBundleName', $this->role->getDomain());
    }

    public function testGetRole()
    {
        $this->assertEquals('ROLE_VENDOR_BUNDLE_TYPE_NAME.PROPERTY_ACTION', $this->role->getRole());
    }
}
