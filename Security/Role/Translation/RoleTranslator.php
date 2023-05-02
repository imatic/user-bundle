<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Security\Role\Translation;

use Imatic\Bundle\UserBundle\Security\Role\Role;
use Symfony\Contracts\Translation\TranslatorInterface;

class RoleTranslator
{
    /** @var TranslationStrategyInterface[] */
    private array $strategies = [];

    private bool $sorted;

    public function __construct(
        private TranslatorInterface $translator
    ) {
    }

    public function addStrategy(TranslationStrategyInterface $strategy): self
    {
        $this->strategies[$strategy->getSupportedClass()] = $strategy;
        $this->sorted = false;

        return $this;
    }

    public function translateRole(Role $role): string
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

    public function translateRoleType(string $roleType): string
    {
        return $this->trans($roleType, 'role_types');
    }

    public function translateRoleDomain(string $domain): string
    {
        $translation = $this->trans('Plural', $domain);

        return $translation === 'Plural' ? $domain : $translation;
    }

    public function translateRoleAction($action): string
    {
        return $this->trans($action, 'role_actions');
    }

    private function trans($label, string $domain = 'roles'): string
    {
        /* @Ignore */
        return $this->translator->trans($label, [], $domain);
    }

    private function compareStrategies(TranslationStrategyInterface $a, TranslationStrategyInterface $b): int
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
