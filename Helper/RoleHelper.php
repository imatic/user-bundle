<?php

namespace Imatic\Bundle\UserBundle\Helper;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\FormView;

/**
 * RoleHelper
 */
class RoleHelper
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var array
     */
    protected $roleHierarchy;

    /**
     * @var array
     */
    protected $baseRoles;

    /**
     * @var string
     */
    protected $translationDomain;

    /**
     * @param TranslatorInterface $translator
     * @param array $roleHierarchy
     * @return RoleHelper
     */
    public function __construct(TranslatorInterface $translator, array $roleHierarchy)
    {
        $this->translator = $translator;
        $this->roleHierarchy = $roleHierarchy;
        $this->translationDomain = 'roles';
        $this->baseRoles = array(
            'ROLE_USER',
            'ROLE_SUPER_ADMIN',
            'ROLE_ALLOWED_TO_SWITCH'
        );
    }

    public function getObjectRoles($object)
    {
        $roles = array();
        foreach ($object->getRoles() as $roleName) {
            if (!empty($this->roleHierarchy[$roleName]) && is_array($this->roleHierarchy[$roleName])) {
                $label = sprintf('%s: %s', $roleName, implode(', ', $this->roleHierarchy[$roleName]));
            } else {
                $label = $roleName;
            }
            $roles[$roleName] = $this->translateLabel($label, $this->translationDomain);
        }
        return $roles;
    }

    /**
     * @param FormView $formView
     * @return FormView[]
     */
    public function getFormRoles(FormView $formView)
    {
        $roles = array();
        foreach ($formView as $form) {
            $form->vars['label'] = $this->translateLabel($form->vars['label'], $this->translationDomain);
            $roles[$form->vars['value']] = $form;
        }

        return $roles;
    }

    /**
     * @param array $roles
     * @return array
     */
    public function organizeRoles(array $roles)
    {
        $modules = array();

        foreach ($roles as $roleName => $role) {
            $roleInfo = $this->parseRole($roleName, $this->baseRoles);
            $moduleName = sprintf('%s.%s', $roleInfo['vendor'], $roleInfo['bundle']);
            $subModuleName = sprintf('%s.%s', $roleInfo['object'], $roleInfo['type']);

            if (!isset($modules[$moduleName])) {
                $modules[$moduleName] = array();
            }
            if (!isset($modules[$moduleName][$subModuleName])) {
                $modules[$moduleName][$subModuleName] = array();
            }

            $modules[$moduleName][$subModuleName][$roleName] = $role;
        }

        // @todo: make sure, common group must be first
        ksort($modules);
        foreach ($modules as &$subModule) {
            ksort($subModule);
            foreach ($subModule as &$roles) {
                ksort($roles);
            }
        }

        return $modules;
    }

    /**
     * @param string $role
     * @param array $baseRoles
     * @return array
     */
    public function parseRole($role, array $baseRoles)
    {
        $roleArray = explode('_', $role);

        if (in_array($role, $baseRoles)) {
            $roleInfo['vendor'] = 'imatic';
            $roleInfo['bundle'] = 'user';
            $roleInfo['type'] = 'common';
            $roleInfo['object'] = 'common';
            $roleInfo['action'] = '';
            $roleInfo['base'] = true;
        } else {
            $roleInfo['vendor'] = strtolower($roleArray[1]);
            $roleInfo['bundle'] = strtolower($roleArray[2]);
            $roleInfo['type'] = strtolower($roleArray[3]);
            $roleInfo['object'] = strtolower($roleArray[4]);
            $roleInfo['action'] = strtolower($roleArray[5]);
            $roleInfo['base'] = false;
        }
        $roleInfo['role'] = $role;

        return $roleInfo;
    }

    /**
     * @param string $label
     * @param string $domain
     * @return string
     */
    public function translateLabel($label, $domain)
    {
        $label = preg_replace_callback('/([A-Z_]+)/', function ($matches) use ($domain) {
            return $this->translateLabelCallback($matches[0], $domain);
        }, $label);

        return $label;
    }

    /**
     * @todo: refactor this
     * @param string $label
     * @param string $domain
     * @return string
     */
    public function translateLabelCallback($label, $domain)
    {
        // translate admin role
//        if (preg_match('/^ROLE_([A-Z]+)_([A-Z]+)_ADMIN_([A-Z]+)_([A-Z]+)$/', $label, $matches)) {
//            $transKey = sprintf('admin_role.%s', strtolower($matches[4]));
//            $transParams = array(
//                '%singular%' => $this->translator->trans(sprintf('admin_object.%s.%s', strtolower($matches[1] . '_' . $matches[2] . '_' . $matches[3]), 'singular'), array(), $domain),
//                '%plural%' => $this->translator->trans(sprintf('admin_object.%s.%s', strtolower($matches[1] . '_' . $matches[2] . '_' . $matches[3]), 'plural'), array(), $domain),
//            );
//            $label = $this->translator->trans($transKey, $transParams, $domain);
//        } else {
//            // translate standard role name
//            $label = $this->translator->trans($label, array(), $domain);
//        }

        $label = ucfirst(strtolower(str_replace(array('_', 'ROLE'), ' ', $label)));

        return $label;
    }
}
