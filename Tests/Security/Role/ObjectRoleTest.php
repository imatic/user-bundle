<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Tests\Security\Role;

use Imatic\Bundle\UserBundle\Security\Role\ObjectRole;
use PHPUnit\Framework\TestCase;

class ObjectRoleTest extends TestCase
{
    private ObjectRole $role;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->role = new ObjectRole('Vendor', 'Bundle', 'type', 'Name', 'property', 'action');
    }

    public function testGetDomain(): void
    {
        $this->assertEquals('VendorBundleBundleName', $this->role->getDomain());
    }

    public function testGetRole(): void
    {
        $this->assertEquals('ROLE_VENDOR_BUNDLE_TYPE_NAME.PROPERTY_ACTION', $this->role->getRole());
    }
}
