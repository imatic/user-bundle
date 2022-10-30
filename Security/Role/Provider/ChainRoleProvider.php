<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Security\Role\Provider;

use Imatic\Bundle\UserBundle\Security\Role\Role;

class ChainRoleProvider implements RoleProviderInterface
{
    /** @var Role[] */
    private array $roles;

    /**
     * @param RoleProviderInterface[] $roleProviders
     */
    public function __construct(
        private array $roleProviders = []
    )
    {
    }

    /**
     * @return Role[]
     */
    public function getRoles(): array
    {
        if (empty($this->roles)) {
            $this->roles = [];

            foreach ($this->roleProviders as $roleProvider) {
                $this->roles = \array_merge($this->roles, $roleProvider->getRoles());
            }
        }

        return $this->roles;
    }

    public function addRoleProvider(RoleProviderInterface $roleProvider): void
    {
        $this->roleProviders[] = $roleProvider;
    }
}
