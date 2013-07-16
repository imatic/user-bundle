<?php
namespace Imatic\Bundle\UserBundle\Twig\Extension;

use Imatic\Bundle\UserBundle\Security\Role\RoleProviderInterface;

class SecurityExtension extends \Twig_Extension
{
    /** @var RoleProviderInterface */
    private $roleProvider;

    /**
     * @param RoleProviderInterface $roleProvider
     */
    public function __construct(RoleProviderInterface $roleProvider)
    {
        $this->roleProvider = $roleProvider;
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
    public function getName()
    {
        return 'imatic_user_security';
    }
}
