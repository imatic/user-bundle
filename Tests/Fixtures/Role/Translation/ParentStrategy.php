<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Tests\Fixtures\Role\Translation;

use Imatic\Bundle\UserBundle\Security\Role\Translation\TranslationStrategyInterface;

class ParentStrategy implements TranslationStrategyInterface
{
    public function translate(object $role): string
    {
        return 'parent';
    }

    public function getSupportedClass(): string
    {
        return \Imatic\Bundle\UserBundle\Tests\Fixtures\Role\ParentRole::class;
    }
}
