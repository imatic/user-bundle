<?php
namespace Imatic\Bundle\UserBundle\Tests\Security\Role\Translation;

use Imatic\Bundle\UserBundle\Security\Role\HierarchyRole;
use Imatic\Bundle\UserBundle\Security\Role\Translation\HierarchyStrategy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\TranslatorInterface;

class HierarchyStrategyTest extends TestCase
{
    /** @var HierarchyStrategy */
    private $strategy;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->strategy = new HierarchyStrategy($this->createTranslatorMock());
    }

    public function testTranslate()
    {
        $this->assertEquals('ROLE', $this->strategy->translate(new HierarchyRole('ROLE')));
        $hierarchy = new HierarchyRole('ROLE', [
            new HierarchyRole('ROLE_A'),
            new HierarchyRole('ROLE_B', [new HierarchyRole('ROLE_A')]),
        ]);
        $this->assertEquals('ROLE (ROLE_A, ROLE_B)', $this->strategy->translate($hierarchy));
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
