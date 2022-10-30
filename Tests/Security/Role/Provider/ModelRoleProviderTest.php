<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Tests\Security\Role\Provider;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\ClassMetadataFactory;
use Imatic\Bundle\UserBundle\Security\Role\ObjectRole;
use Imatic\Bundle\UserBundle\Security\Role\Provider\ModelRoleProvider;
use PHPUnit\Framework\TestCase;

class ModelRoleProviderTest extends TestCase
{
    private ModelRoleProvider $roleProvider;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->roleProvider = new ModelRoleProvider($this->createClassMetadataFactoryMock());
    }

    public function testGetRoles(): void
    {
        $this->assertEmpty($this->roleProvider->getRoles());
        $this->roleProvider->setConfig([
            'namespaces' => ['includes' => ['Vendor\Foo\Entity\ClassA']],
            'properties' => ['includes' => ['Vendor\Foo\Entity\ClassA' => ['propertyA']]],
        ]);
        $this->assertEquals(
            [
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'propertyA', 'show'),
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'propertyA', 'edit'),
            ],
            $this->roleProvider->getRoles()
        );
        $this->roleProvider->setConfig([
            'namespaces' => ['includes' => ['Vendor']],
            'properties' => ['excludes' => ['Vendor\Foo\Entity\ClassA' => ['propertyB']]],
        ]);
        $this->assertEquals(
            [
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'propertyA', 'show'),
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'propertyA', 'edit'),
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'association', 'show'),
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'association', 'edit'),
                new ObjectRole('Vendor', 'Foo', 'entity', 'Bar_ClassB', 'property', 'show'),
                new ObjectRole('Vendor', 'Foo', 'entity', 'Bar_ClassB', 'property', 'edit'),
            ],
            $this->roleProvider->getRoles()
        );
    }

    public function testGetRolesShouldBeFiltered(): void
    {
        $this->roleProvider->setConfig(['namespaces' => ['includes' => ['Foo']]]);
        $this->assertEmpty($this->roleProvider->getRoles());
        $this->roleProvider->setConfig(['namespaces' => [
            'includes' => ['Vendor'],
            'excludes' => ['Vendor\Foo\Entity\Bar'],
        ]]);
        $this->assertEquals(
            [
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'propertyA', 'show'),
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'propertyA', 'edit'),
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'propertyB', 'show'),
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'propertyB', 'edit'),
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'association', 'show'),
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'association', 'edit'),
            ],
            $this->roleProvider->getRoles()
        );
    }

    public function testGetRolesShouldContainGroupsOnly(): void
    {
        $this->roleProvider->setConfig([
            'namespaces' => [
                'includes' => ['Vendor\Foo\Entity\ClassA'],
                'excludes' => [],
            ],
            'properties' => [
                'groups' => ['Vendor\Foo\Entity\ClassA' => ['group' => ['propertyA', 'propertyB', 'association']]],
            ],
        ]);
        $this->assertEquals(
            [
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'group', 'show'),
                new ObjectRole('Vendor', 'Foo', 'entity', 'ClassA', 'group', 'edit'),
            ],
            $this->roleProvider->getRoles()
        );
    }

    public function testGetRole(): void
    {
        $this->roleProvider->setConfig([
            'namespaces' => ['includes' => ['Vendor']],
            'properties' => ['groups' => ['Vendor\Foo\Entity\ClassA' => ['group' => ['propertyA', 'propertyB']]]],
        ]);
        $this->assertNull($this->roleProvider->getRole('Vendor\Foo\Entity\Bar\ClassB', 'property', 'list'));
        $this->assertNotNull($this->roleProvider->getRole('Vendor\Foo\Entity\Bar\ClassB', 'property', 'show'));
        $this->assertNotNull($this->roleProvider->getRole('Vendor\Foo\Entity\Bar\ClassB', 'property', 'edit'));
        $role = $this->roleProvider->getRole('Vendor\Foo\Entity\ClassA', 'propertyA', 'show');
        $this->assertNotNull($role);
        $this->assertEquals('group', $role->getProperty());
    }

    private function createClassMetadataFactoryMock():  PHPUnit\Framework\MockObject\MockObject
    {
        $metadataFactoryMock = $this->createMock(ClassMetadataFactory::class);
        $aMetadata = new ClassMetadata(ClassA::class);
        $aMetadata->mapField(['fieldName' => 'propertyA']);
        $aMetadata->mapField(['fieldName' => 'propertyB']);
        $aMetadata->mapManyToOne(['fieldName' => 'association', 'targetEntity' => '']);
        $bMetadata = new ClassMetadata(ClassB::class);
        $bMetadata->mapField(['fieldName' => 'property']);
        $metadataFactoryMock
            ->expects($this->any())
            ->method('getAllMetadata')
            ->will($this->returnValue([$aMetadata, $bMetadata]));
        $metadataFactoryMock
            ->expects($this->any())
            ->method('getMetadataFor')
            ->will($this->returnValue($aMetadata));

        return $metadataFactoryMock;
    }
}
