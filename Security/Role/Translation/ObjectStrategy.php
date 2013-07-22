<?php
namespace Imatic\Bundle\UserBundle\Security\Role\Translation;

use Imatic\Bundle\UserBundle\Security\Role\ObjectRole;
use Sonata\AdminBundle\Translator\LabelTranslatorStrategyInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ObjectStrategy extends TranslationStrategy
{
    /** @var LabelTranslatorStrategyInterface */
    private $labelTranslatorStrategy;

    /**
     * @param TranslatorInterface $translator
     * @param LabelTranslatorStrategyInterface $labelTranslatorStrategy
     */
    public function __construct(
        TranslatorInterface $translator,
        LabelTranslatorStrategyInterface $labelTranslatorStrategy
    ) {
        parent::__construct($translator);
        $this->labelTranslatorStrategy = $labelTranslatorStrategy;
    }

    /**
     * {@inheritDoc}
     */
    public function getSupportedClass()
    {
        return 'Imatic\Bundle\UserBundle\Security\Role\ObjectRole';
    }

    /**
     * @param ObjectRole $role
     * @return string
     */
    protected function doTranslate($role)
    {
        if ($role->getProperty() != '') {
            return $this->trans($this->labelTranslatorStrategy->getLabel($role->getLabel()), $role->getDomain());
        }

       /** @Ignore */
        return $this->trans($role->getLabel(), 'role_actions');
    }
}