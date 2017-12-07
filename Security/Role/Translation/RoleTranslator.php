<?php
namespace Imatic\Bundle\UserBundle\Security\Role\Translation;

use Imatic\Bundle\UserBundle\Security\Role\Role;
use Symfony\Component\Translation\TranslatorInterface;

class RoleTranslator
{
    /** @var TranslatorInterface */
    private $translator;

    /** @var TranslationStrategyInterface[] */
    private $strategies = [];

    /** @var bool */
    private $sorted;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param TranslationStrategyInterface $strategy
     *
     * @return $this
     */
    public function addStrategy(TranslationStrategyInterface $strategy)
    {
        $this->strategies[$strategy->getSupportedClass()] = $strategy;
        $this->sorted = false;

        return $this;
    }

    /**
     * @param Role $role
     *
     * @return string
     */
    public function translateRole(Role $role)
    {
        if (!$this->sorted) {
            \uasort($this->strategies, function ($a, $b) {
                return $this->compareStrategies($a, $b);
            });
            $this->sorted = true;
        }

        foreach ($this->strategies as $class => $strategy) {
            if ($role instanceof $class) {
                return $strategy->translate($role);
            }
        }

        return $this->trans($role->getRole());
    }

    /**
     * @param string $roleType
     *
     * @return string
     */
    public function translateRoleType($roleType)
    {
        return $this->trans($roleType, 'role_types');
    }

    /**
     * @param string $domain
     *
     * @return string
     */
    public function translateRoleDomain($domain)
    {
        $translation = $this->trans('Plural', $domain);

        return $translation === 'Plural' ? $domain : $translation;
    }

    /**
     * @param $action
     *
     * @return string
     */
    public function translateRoleAction($action)
    {
        return $this->trans($action, 'role_actions');
    }

    /**
     * @param $label
     * @param string $domain
     *
     * @return string
     */
    private function trans($label, $domain = 'roles')
    {
        /* @Ignore */
        return $this->translator->trans($label, [], $domain);
    }

    /**
     * @param TranslationStrategyInterface $a
     * @param TranslationStrategyInterface $b
     *
     * @return int
     */
    private function compareStrategies(TranslationStrategyInterface $a, TranslationStrategyInterface $b)
    {
        $aClass = $a->getSupportedClass();
        $bClass = $b->getSupportedClass();

        if (\is_subclass_of($aClass, $bClass)) {
            return -1;
        } elseif (\is_subclass_of($bClass, $aClass)) {
            return 1;
        }

        return 0;
    }
}
