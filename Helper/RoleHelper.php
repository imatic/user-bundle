<?php

namespace Imatic\Bundle\UserBundle\Helper;

use Imatic\Bundle\UserBundle\Entity\User;
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
    protected $commonRoles;

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
        $this->commonRoles = array(
            'ROLE_SUPER_ADMIN',
            'ROLE_ALLOWED_TO_SWITCH'
        );
    }

    public function getUserRoles(User $user)
    {
        $roles = array();
        foreach ($user->getRoles() as $roleName) {
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
            $moduleInfo = $this->getModuleInfo($roleName);
            $moduleName = $moduleInfo['module'];
            $subModuleName = $moduleInfo['submodule'];

            if (!isset($modules[$moduleName])) {
                $modules[$moduleName] = array();
            }
            if (!isset($modules[$moduleName][$subModuleName])) {
                $modules[$moduleName][$subModuleName] = array();
            }

            $modules[$moduleName][$subModuleName][$roleName] = $role;
        }

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
     * Role name format
     * ROLE_$BUNDLENAMESPACE_$BUNDLENAME_($TYPE)?_$OBJECT_$ACTION
     * => module/$BUNDLENAMESPACE_$BUNDLENAME/$OBJECT
     *
     * @param string $roleName
     * @return array
     */
    public function getModuleInfo($roleName)
    {
        if (in_array($roleName, $this->commonRoles)) {
            $module = 'modules.common.name';
            $subModule = '';
        } else {
            $roleNameArray = explode('_', $roleName);
            $module = strtolower('modules.' . implode('_', array($roleNameArray[1], $roleNameArray[2])) . '.name');
            $subModule = strtolower('modules.' . implode('_', array($roleNameArray[1], $roleNameArray[2])) . '.submodules.' . $roleNameArray[count($roleNameArray) - 2]);
        }

        $module = $this->translator->trans($module, array(), $this->translationDomain);
        $subModule = $this->translator->trans($subModule, array(), $this->translationDomain);

        return array('module' => $module, 'submodule' => $subModule);
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
     * @todo: allow custom translation strategies and refactor this
     * @param string $label
     * @param string $domain
     * @return string
     */
    public function translateLabelCallback($label, $domain)
    {
        // translate admin role
        if (preg_match('/^ROLE_([A-Z]+)_([A-Z]+)_ADMIN_([A-Z]+)_([A-Z]+)$/', $label, $matches)) {
            $transKey = sprintf('admin_role.%s', strtolower($matches[4]));
            $transParams = array(
                '%singular%' => $this->translator->trans(sprintf('admin_object.%s.%s', strtolower($matches[1] . '_' . $matches[2] . '_' . $matches[3]), 'singular'), array(), $domain),
                '%plural%' => $this->translator->trans(sprintf('admin_object.%s.%s', strtolower($matches[1] . '_' . $matches[2] . '_' . $matches[3]), 'plural'), array(), $domain),
            );
            $label = $this->translator->trans($transKey, $transParams, $domain);
        } else {
            // translate standard role name
            $label = $this->translator->trans($label, array(), $domain);
        }

        return $label;
    }
}
