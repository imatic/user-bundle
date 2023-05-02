<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Security\Role\Translation;

use Imatic\Bundle\UserBundle\Security\Role\HierarchyRole;

class HierarchyStrategy extends TranslationStrategy
{
    protected function doTranslate(HierarchyRole $role, ?bool $deep = true): string
    {
        $translation = $this->trans($role->getLabel());
        $children = $role->getChildren();

        if ($deep && $children) {
            $translation .= \sprintf(' (%s)', \implode(', ', \array_map([$this, 'doTranslate'], $children, [false])));
        }

        return $translation;
    }

    public function getSupportedClass(): string
    {
        return HierarchyRole::class;
    }
}
