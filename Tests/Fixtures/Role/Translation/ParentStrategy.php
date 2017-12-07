<?php
namespace Imatic\Bundle\UserBundle\Tests\Fixtures\Role\Translation;

use Imatic\Bundle\UserBundle\Security\Role\Translation\TranslationStrategyInterface;

class ParentStrategy implements TranslationStrategyInterface
{
    /**
     * @param object $role
     *
     * @return string
     */
    public function translate($role)
    {
        return 'parent';
    }

    /**
     * @return string
     */
    public function getSupportedClass()
    {
        return 'Imatic\Bundle\UserBundle\Tests\Fixtures\Role\ParentRole';
    }
}
