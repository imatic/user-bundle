<?php
namespace Imatic\Bundle\UserBundle\Tests\Security\Role;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Imatic\Bundle\UserBundle\Security\Role\ModelRoleProvider;
use Imatic\Bundle\UserBundle\Security\Role\ObjectRole;

class ModelRoleProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ModelRoleProvider
     */
    private $roleProvider;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->roleProvider = new ModelRoleProvider($this->createClassMetadataFactoryMock());
    }

    public function testGetRoles()
    {
        $this->assertEmpty($this->roleProvider->getRoles());
        $this->roleProvider->setConfig([ModelRoleProvider::CONFIG_INCLUDES => 'Vendor']);
        $this->assertEquals(
            [
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'propertyA', 'show'),
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'propertyA', 'edit'),
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'propertyB', 'show'),
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'propertyB', 'edit'),
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'association', 'show'),
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'association', 'edit'),
                new ObjectRole('Vendor', 'Foo', 'entity', 'Bar_ClassB', 'property', 'show'),
                new ObjectRole('Vendor', 'Foo', 'entity', 'Bar_ClassB', 'property', 'edit')
            ],
            $this->roleProvider->getRoles()
        );
    }

    public function testGetRolesShouldBeFiltered()
    {
        $this->roleProvider->setConfig([ModelRoleProvider::CONFIG_INCLUDES => 'Foo']);
        $this->assertEmpty($this->roleProvider->getRoles());
        $this->roleProvider->setConfig([
            ModelRoleProvider::CONFIG_INCLUDES => ['Vendor'],
            ModelRoleProvider::CONFIG_EXCLUDES => ['Vendor\Foo\Entity\Bar']
        ]);
        $this->assertEquals(
            [
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'propertyA', 'show'),
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'propertyA', 'edit'),
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'propertyB', 'show'),
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'propertyB', 'edit'),
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'association', 'show'),
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'association', 'edit')
            ],
            $this->roleProvider->getRoles()
        );
    }

    public function testGetRolesShouldContainGroupsOnly()
    {
        $this->roleProvider->setConfig([
            ModelRoleProvider::CONFIG_INCLUDES => ['Vendor\Foo\Entity\ClassA'],
            ModelRoleProvider::CONFIG_EXCLUDES => [],
            ModelRoleProvider::CONFIG_GROUPS => [
                'Vendor\Foo\Entity\ClassA' => ['group' => ['propertyA', 'propertyB', 'association']]
            ]
        ]);
        $this->assertEquals(
            [
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'group', 'show'),
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'group', 'edit')
            ],
            $this->roleProvider->getRoles()
        );
    }

    public function testGetRole()
    {
        $this->roleProvider->setConfig([
            ModelRoleProvider::CONFIG_INCLUDES => ['Vendor'],
            ModelRoleProvider::CONFIG_GROUPS => ['Vendor\Foo\Entity\ClassA' => ['group' => ['propertyA', 'propertyB']]]
        ]);
        $this->assertNull($this->roleProvider->getRole('Vendor\Foo\Entity\Bar\ClassB', 'property', 'list'));
        $this->assertNotNull($this->roleProvider->getRole('Vendor\Foo\Entity\Bar\ClassB', 'property', 'show'));
        $this->assertNotNull($this->roleProvider->getRole('Vendor\Foo\Entity\Bar\ClassB', 'property', 'edit'));
        $role = $this->roleProvider->getRole('Vendor\Foo\Entity\ClassA', 'propertyA', 'show');
        $this->assertNotNull($role);
        $this->assertEquals('group', $role->getProperty());
    }

    /**
     * @return ClassMetadataFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createClassMetadataFactoryMock()
    {
        $metadataFactoryMock = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadataFactory');
        $aMetadata = new ClassMetadata('Vendor\Foo\Entity\ClassA');
        $aMetadata->mapField(['fieldName' => 'propertyA']);
        $aMetadata->mapField(['fieldName' => 'propertyB']);
        $aMetadata->mapManyToOne(['fieldName' => 'association', 'targetEntity' => '']);
        $bMetadata = new ClassMetadata('Vendor\Foo\Entity\Bar\ClassB');
        $bMetadata->mapField(['fieldName' => 'property']);
        $metadataFactoryMock
            ->expects($this->any())
            ->method('getAllMetadata')->will($this->returnValue([$aMetadata, $bMetadata]))
        ;
        $metadataFactoryMock
            ->expects($this->any())
            ->method('getMetadataFor')->will($this->returnValue($aMetadata))
        ;

        return $metadataFactoryMock;
    }
}
