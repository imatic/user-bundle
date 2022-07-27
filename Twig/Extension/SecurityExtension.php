<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Twig\Extension;

use Imatic\Bundle\UserBundle\Security\Role\Provider\RoleProviderInterface;
use Imatic\Bundle\UserBundle\Security\Role\Translation\RoleTranslator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class SecurityExtension extends AbstractExtension
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
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [new TwigFunction('get_role', [$this->roleProvider, 'getRole'])];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('trans_role', [$this->roleTranslator, 'translateRole']),
            new TwigFilter('trans_role_type', [$this->roleTranslator, 'translateRoleType']),
            new TwigFilter('trans_role_domain', [$this->roleTranslator, 'translateRoleDomain']),
            new TwigFilter('trans_role_action', [$this->roleTranslator, 'translateRoleAction']),
        ];
    }
}
