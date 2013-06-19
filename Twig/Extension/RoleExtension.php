<?php

namespace Imatic\Bundle\UserBundle\Twig\Extension;

use Twig_Extension;
use Twig_Function_Method;
use Imatic\Bundle\UserBundle\Helper\RoleHelper;
use Symfony\Component\Form\FormView;

/**
 * RoleExtension
 */
class RoleExtension extends Twig_Extension
{
    /**
     * @var RoleHelper
     */
    protected $roleHelper;

    /**
     * @param RoleHelper $helper
     */
    public function __construct(RoleHelper $helper)
    {
        $this->roleHelper = $helper;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'imatic_user_get_form_roles' => new Twig_Function_Method($this, 'getFormRoles'),
            'imatic_user_get_object_roles' => new Twig_Function_Method($this, 'getObjectRoles'),
            'imatic_user_organize_roles' => new Twig_Function_Method($this, 'organizeRoles')
        );
    }

    /**
     * @param  object $object
     * @return array
     */
    public function getObjectRoles($object)
    {
        return $this->roleHelper->getObjectRoles($object);
    }

    /**
     * @param  \Symfony\Component\Form\FormView   $form
     * @return \Symfony\Component\Form\FormView[]
     */
    public function getFormRoles(FormView $form)
    {
        return $this->roleHelper->getFormRoles($form);
    }

    /**
     * @param  array $roles
     * @return array
     */
    public function organizeRoles(array $roles)
    {
        return $this->roleHelper->organizeRoles($roles);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'imatic_user_role';
    }
}
