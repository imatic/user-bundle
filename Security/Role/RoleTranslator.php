<?php
namespace Imatic\Bundle\UserBundle\Security\Role;

use Sonata\AdminBundle\Translator\LabelTranslatorStrategyInterface;
use Symfony\Component\Translation\TranslatorInterface;

class RoleTranslator
{
    /** @var TranslatorInterface */
    private $translator;

    /** @var LabelTranslatorStrategyInterface */
    private $translatorStrategy;

    /**
     * @param TranslatorInterface $translator
     * @param LabelTranslatorStrategyInterface $translatorStrategy
     */
    public function __construct(TranslatorInterface $translator, LabelTranslatorStrategyInterface $translatorStrategy)
    {
        $this->translator = $translator;
        $this->translatorStrategy = $translatorStrategy;
    }

    /**
     * @param Role $role
     * @return string
     */
    public function translateRole(Role $role)
    {
        $translation = $this->translator->trans($role->getRole(), [], 'roles');
        $label = $this->translatorStrategy->getLabel($role->getProperty());

        if ($translation != $role->getRole()) {
            return $translation;
        }

        return sprintf(
            '%s – %s',
            $this->translator->trans($label, [], $role->getAbsoluteName()),
            $this->translator->trans($role->getAction(), [], 'role_actions')
        );
    }

    /**
     * @param string $roleType
     * @return string
     */
    public function translateRoleType($roleType)
    {
        return $this->translator->trans($roleType, [], 'role_types');
    }

    /**
     * @param string $roleName
     * @return string
     */
    public function translateRoleAbsoluteName($roleName)
    {
        $translation = $this->translator->trans('Plural', [], $roleName);

        return $translation == 'Plural' ? $roleName : $translation;
    }
}