<?php
namespace Imatic\Bundle\UserBundle\Tests\Security\Role;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Imatic\Bundle\UserBundle\Security\Role\Configuration;
use Imatic\Bundle\UserBundle\Security\Role\ModelRoleProvider;
use Imatic\Bundle\UserBundle\Security\Role\Role;

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
        $this->assertEquals(
            [
                new Role('Vendor', 'Foo', 'entity', 'ClassA', 'show', 'propertyA'),
                new Role('Vendor', 'Foo', 'entity', 'ClassA', 'edit', 'propertyA'),
                new Role('Vendor', 'Foo', 'entity', 'ClassA', 'show', 'propertyB'),
                new Role('Vendor', 'Foo', 'entity', 'ClassA', 'edit', 'propertyB'),
                new Role('Vendor', 'Foo', 'entity', 'ClassA', 'show', 'association'),
                new Role('Vendor', 'Foo', 'entity', 'ClassA', 'edit', 'association'),
                new Role('Vendor', 'Foo', 'entity', 'Bar_ClassB', 'show', 'property'),
                new Role('Vendor', 'Foo', 'entity', 'Bar_ClassB', 'edit', 'property')
            ],
            $this->roleProvider->getRoles()
        );
        $this->assertEquals(
            [
                new Role('Vendor', 'Foo', 'entity', 'Bar_ClassB', 'show', 'property'),
                new Role('Vendor', 'Foo', 'entity', 'Bar_ClassB', 'edit', 'property')
            ],
            $this->roleProvider->getRoles('Vendor\Foo\Entity\Bar\ClassB')
        );
    }

    public function testGetRolesShouldBeFiltered()
    {
        $this->roleProvider->setConfiguration(new Configuration(['Vendor']));
        $this->assertEmpty($this->roleProvider->getRoles());
        $this->roleProvider->setConfiguration(new Configuration(['Vendor\Foo'], ['Vendor\Foo\Entity\Bar']));
        $this->assertEquals(
            [
                new Role('Vendor', 'Foo', 'entity', 'Bar_ClassB', 'show', 'property'),
                new Role('Vendor', 'Foo', 'entity', 'Bar_ClassB', 'edit', 'property')
            ],
            $this->roleProvider->getRoles()
        );
    }

    public function testGetRolesShouldContainGroupsOnly()
    {
        $this->roleProvider->setConfiguration(new Configuration(
            ['Vendor'],
            ['Vendor\Foo\Entity\ClassA'],
            ['Vendor\Foo\Entity\ClassA' => ['group' => ['propertyA', 'propertyB', 'association']]]
        ));
        $this->assertEquals(
            [
                new Role('Vendor', 'Foo', 'entity', 'ClassA', 'show', 'group'),
                new Role('Vendor', 'Foo', 'entity', 'ClassA', 'edit', 'group')
            ],
            $this->roleProvider->getRoles()
        );
    }

    public function testGetRole()
    {
        $this->roleProvider->setConfiguration(new Configuration([], [], [
            'Vendor\Foo\Entity\ClassA' => ['group' => ['propertyA', 'propertyB']]
        ]));
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
