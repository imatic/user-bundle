<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Security\Role\Translation;

use \Symfony\Contracts\Translation\TranslatorInterface;

abstract class TranslationStrategy implements TranslationStrategyInterface
{
    public function __construct(
        private TranslatorInterface $translator
    )
    {
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function translate(object $role): string
    {
        $supportedClass = $this->getSupportedClass();

        if (!$role instanceof $supportedClass) {
            throw new \InvalidArgumentException(\sprintf(
                'Expected argument of type "%s", "%s" given.',
                $supportedClass,
                \is_object($role) ? \get_class($role) : \gettype($role)
            ));
        }

        return $this->doTranslate($role);
    }

    abstract protected function doTranslate(\Imatic\Bundle\UserBundle\Security\Role\HierarchyRole $role): string;

    protected function trans($label, string $domain = 'roles'): string
    {
        /* @Ignore */
        return $this->translator->trans($label, [], $domain);
    }
}
