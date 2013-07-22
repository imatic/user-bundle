<?php
namespace Imatic\Bundle\UserBundle\Tests\Security\Role\Translation;

use Imatic\Bundle\UserBundle\Security\Role\Translation\RoleTranslator;
use Imatic\Bundle\UserBundle\Tests\Fixtures\Role\ChildRole;
use Imatic\Bundle\UserBundle\Tests\Fixtures\Role\ParentRole;
use Imatic\Bundle\UserBundle\Tests\Fixtures\Role\Translation\ChildStrategy;
use Imatic\Bundle\UserBundle\Tests\Fixtures\Role\Translation\ParentStrategy;
use Symfony\Component\Translation\TranslatorInterface;

class RoleTranslatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var RoleTranslator */
    private $roleTranslator;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->roleTranslator = new RoleTranslator($this->createTranslatorMock());
        $this->roleTranslator
            ->addStrategy(new ChildStrategy())
            ->addStrategy(new ParentStrategy())
        ;
    }

    public function testTranslateRole()
    {
        $this->assertEquals('child', $this->roleTranslator->translateRole(new ChildRole('ROLE')));
        $this->assertEquals('parent', $this->roleTranslator->translateRole(new ParentRole('ROLE')));
    }

    public function testTranslateRoleType()
    {
        $this->assertEquals('type', $this->roleTranslator->translateRoleType('type'));
    }

    public function testTranslateRoleDomain()
    {
        $this->assertEquals('domain', $this->roleTranslator->translateRoleDomain('domain'));
    }

    public function testTranslateRoleAction()
    {
        $this->assertEquals('action', $this->roleTranslator->translateRoleDomain('action'));
    }

    /**
     * @return TranslatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createTranslatorMock()
    {
        $translatorMock = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $translatorMock
            ->expects($this->any())
            ->method('trans')
            ->will($this->returnArgument(0))
        ;

        return $translatorMock;
    }
}