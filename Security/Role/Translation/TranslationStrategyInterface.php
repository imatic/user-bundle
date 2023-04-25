<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Security\Role\Translation;

interface TranslationStrategyInterface
{
    public function translate(object $role): mixed;

    public function getSupportedClass(): string;
}
