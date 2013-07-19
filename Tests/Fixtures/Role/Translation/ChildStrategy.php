<?php
namespace Imatic\Bundle\UserBundle\Tests\Fixtures\Role\Translation;

use Imatic\Bundle\UserBundle\Security\Role\Translation\TranslationStrategyInterface;

class ChildStrategy implements TranslationStrategyInterface
{
    /**
     * @param object $role
     * @return string
     */
    public function translate($role)
    {
        return 'child';
    }

    /**
     * @return string
     */
    public function getSupportedClass()
    {
        return 'Imatic\Bundle\UserBundle\Tests\Fixtures\Role\ChildRole';
    }
}