<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Tests\Security\Role\Translation;

use Imatic\Bundle\UserBundle\Security\Role\HierarchyRole;
use Imatic\Bundle\UserBundle\Security\Role\Translation\HierarchyStrategy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\TranslatorInterface;

class HierarchyStrategyTest extends TestCase
{
    private HierarchyStrategy $strategy;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->strategy = new HierarchyStrategy($this->createTranslatorMock());
    }

    public function testTranslate(): void
    {
        $this->assertEquals('ROLE', $this->strategy->translate(new HierarchyRole('ROLE')));
        $hierarchy = new HierarchyRole('ROLE', [
            new HierarchyRole('ROLE_A'),
            new HierarchyRole('ROLE_B', [new HierarchyRole('ROLE_A')]),
        ]);
        $this->assertEquals('ROLE (ROLE_A, ROLE_B)', $this->strategy->translate($hierarchy));
    }

    private function createTranslatorMock(): \PHPUnit\Framework\MockObject\MockObject
    {
        $translatorMock = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $translatorMock
            ->expects($this->any())
            ->method('trans')
            ->will($this->returnArgument(0));

        return $translatorMock;
    }
}
