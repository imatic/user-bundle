<?php
namespace Imatic\Bundle\UserBundle\Security\Role;

use Sonata\AdminBundle\Translator\LabelTranslatorStrategyInterface;
use Symfony\Component\Translation\TranslatorInterface;

class RoleTranslator
{
    /** @var TranslatorInterface */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param Role $role
     * @return string
     */
    public function translateRole(Role $role, $showChildren = true)
    {
        $translation = $this->translator->trans($role->getRole(), [], 'roles');
        $action = $role->getAction();
        $children = $role->getChildren();

        if ($translation == $role->getRole()) {
            $translation = $this->translator->trans($role->getLabel(), [], $role->getDomain());
        }

        if ($action != '') {
            if ($translation != '') {
                $translation .= ' – ';
            }

            $translation .= $this->translator->trans($role->getAction(), [], 'role_actions');
        }

        if ($showChildren && $children) {
            $translation .= sprintf(' (%s)', implode(', ', array_map([$this, 'translateRole'], $children, [false])));
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