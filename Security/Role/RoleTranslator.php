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
        $label = $this->translatorStrategy->getLabel($role->getLabel());
        $action = $role->getAction();

        if ($translation != $role->getRole()) {
            return $translation;
        }

        $translation = $this->translator->trans($label, [], $role->getDomain());

        if ($action != '') {
            $translation .= sprintf(' – %s', $this->translator->trans($role->getAction(), [], 'role_actions'));
        }

        return $translation;
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
     * @param string $domain
     * @return string
     */
    public function translateRoleDomain($domain)
    {
        $translation = $this->translator->trans('Plural', [], $domain);

        return $translation == 'Plural' ? $domain : $translation;
    }
}