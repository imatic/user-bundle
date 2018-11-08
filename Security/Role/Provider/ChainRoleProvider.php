<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Security\Role\Provider;

use Imatic\Bundle\UserBundle\Security\Role\Role;

class ChainRoleProvider implements RoleProviderInterface
{
    /** @var RoleProviderInterface[] */
    private $roleProviders;

    /** @var Role[] */
    private $roles;

    /**
     * @param RoleProviderInterface[] $roleProviders
     */
    public function __construct(array $roleProviders = [])
    {
        $this->roleProviders = $roleProviders;
    }

    /**
     * @return Role[]
     */
    public function getRoles()
    {
        if ($this->roles === null) {
            $this->roles = [];

            foreach ($this->roleProviders as $roleProvider) {
                $this->roles = \array_merge($this->roles, $roleProvider->getRoles());
            }
        }

        return $this->roles;
    }

    /**
     * @param RoleProviderInterface $roleProvider
     */
    public function addRoleProvider(RoleProviderInterface $roleProvider): void
    {
        $this->roleProviders[] = $roleProvider;
    }
}
