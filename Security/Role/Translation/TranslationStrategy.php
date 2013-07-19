<?php
namespace Imatic\Bundle\UserBundle\Security\Role\Translation;

use Symfony\Component\Translation\TranslatorInterface;

abstract class TranslationStrategy implements TranslationStrategyInterface
{
    /** @var TranslatorInterface $translator */
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param object $role
     * @return string
     * @throws \InvalidArgumentException
     */
    public function translate($role)
    {
        $supportedClass = $this->getSupportedClass();

        if (!$role instanceof $supportedClass) {
            throw new \InvalidArgumentException(sprintf(
                'Expected argument of type "%s", "%s" given.',
                $supportedClass,
                is_object($role) ? get_class($role) : gettype($role)
            ));
        }

        return $this->doTranslate($role);
    }

    /**
     * @param object $role
     * @return string
     */
    abstract protected function doTranslate($role);

    /**
     * @param $label
     * @param string $domain
     * @return string
     */
    protected function trans($label, $domain = 'roles')
    {
        /** @Ignore */
        return $this->translator->trans($label, [], $domain);
    }
}