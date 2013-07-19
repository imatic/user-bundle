<?php
namespace Imatic\Bundle\UserBundle\Twig\Extension;

use Imatic\Bundle\UserBundle\Security\Role\Provider\RoleProviderInterface;
use Imatic\Bundle\UserBundle\Security\Role\Translation\RoleTranslator;

class SecurityExtension extends \Twig_Extension
{
    /** @var RoleProviderInterface */
    private $roleProvider;

    /** @var RoleTranslator */
    private $roleTranslator;

    /**
     * @param RoleProviderInterface $roleProvider
     */
    public function __construct(RoleProviderInterface $roleProvider, RoleTranslator $roleTranslator)
    {
        $this->roleProvider = $roleProvider;
        $this->roleTranslator = $roleTranslator;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [new \Twig_SimpleFunction('get_role', [$this->roleProvider, 'getRole'])];
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('trans_role', [$this->roleTranslator, 'translateRole']),
            new \Twig_SimpleFilter('trans_role_type', [$this->roleTranslator, 'translateRoleType']),
            new \Twig_SimpleFilter('trans_role_domain', [$this->roleTranslator, 'translateRoleDomain'])
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'imatic_user_security';
    }
}
