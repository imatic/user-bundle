<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Security\Role\Translation;

interface TranslationStrategyInterface
{
    /**
     * @param object $role
     *
     * @return mixed
     */
    public function translate($role);

    /**
     * @return string
     */
    public function getSupportedClass();
}
