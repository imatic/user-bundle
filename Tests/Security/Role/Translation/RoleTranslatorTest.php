<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Tests\Security\Role\Translation;

use Imatic\Bundle\UserBundle\Security\Role\Translation\RoleTranslator;
use Imatic\Bundle\UserBundle\Tests\Fixtures\Role\ChildRole;
use Imatic\Bundle\UserBundle\Tests\Fixtures\Role\ParentRole;
use Imatic\Bundle\UserBundle\Tests\Fixtures\Role\Translation\ChildStrategy;
use Imatic\Bundle\UserBundle\Tests\Fixtures\Role\Translation\ParentStrategy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\TranslatorInterface;

class RoleTranslatorTest extends TestCase
{
    /** @var RoleTranslator */
    private $roleTranslator;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->roleTranslator = new RoleTranslator($this->createTranslatorMock());
        $this->roleTranslator
            ->addStrategy(new ChildStrategy())
            ->addStrategy(new ParentStrategy());
    }

    public function testTranslateRole(): void
    {
        $this->assertEquals('child', $this->roleTranslator->translateRole(new ChildRole('ROLE')));
        $this->assertEquals('parent', $this->roleTranslator->translateRole(new ParentRole('ROLE')));
    }

    public function testTranslateRoleType(): void
    {
        $this->assertEquals('type', $this->roleTranslator->translateRoleType('type'));
    }

    public function testTranslateRoleDomain(): void
    {
        $this->assertEquals('domain', $this->roleTranslator->translateRoleDomain('domain'));
    }

    public function testTranslateRoleAction(): void
    {
        $this->assertEquals('action', $this->roleTranslator->translateRoleDomain('action'));
    }

    /**
     * @return TranslatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createTranslatorMock()
    {
        $translatorMock = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $translatorMock
            ->expects($this->any())
            ->method('trans')
            ->will($this->returnArgument(0));

        return $translatorMock;
    }
}
