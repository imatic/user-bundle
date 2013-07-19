<?php
namespace Imatic\Bundle\UserBundle\Security\Role\Translation;

use Imatic\Bundle\UserBundle\Security\Role\HierarchyRole;

class HierarchyStrategy extends TranslationStrategy
{
    /**
     * {@inheritDoc}
     */
    public function getSupportedClass()
    {
        return 'Imatic\Bundle\UserBundle\Security\Role\HierarchyRole';
    }

    /**
     * @param HierarchyRole $role
     * @param bool $deep
     * @return string
     */
    protected function doTranslate($role, $deep = true)
    {
        $translation = $this->trans($role->getRole());
        $children = $role->getChildren();

        if ($deep && $children) {
            $translation .= sprintf(' (%s)', implode(', ', array_map([$this, 'doTranslate'], $children, [false])));
        }

        return $translation;
    }
}