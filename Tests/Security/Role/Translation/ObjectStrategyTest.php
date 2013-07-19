<?php
namespace Imatic\Bundle\UserBundle\Tests\Security\Role\Translation;

use Imatic\Bundle\UserBundle\Security\Role\ObjectRole;
use Imatic\Bundle\UserBundle\Security\Role\SonataRole;
use Imatic\Bundle\UserBundle\Security\Role\Translation\ObjectStrategy;
use Sonata\AdminBundle\Translator\NoopLabelTranslatorStrategy;
use Symfony\Component\Translation\TranslatorInterface;

class ObjectStrategyTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjectStrategy */
    private $strategy;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->strategy = new ObjectStrategy($this->createTranslatorMock(), new NoopLabelTranslatorStrategy());
    }

    public function testTranslate()
    {
        $this->assertEquals(
            'action',
            $this->strategy->translate(new SonataRole('vendor', 'bundle', 'type', 'name', 'action', 'role'))
        );
        $this->assertEquals(
            'property – action',
            $this->strategy->translate(new ObjectRole('vendor', 'bundle', 'type', 'name', 'property', 'action'))
        );
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