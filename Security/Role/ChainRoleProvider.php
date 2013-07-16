<?php
namespace Imatic\Bundle\UserBundle\Security\Role;

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
                $this->roles = array_merge($this->roles, $roleProvider->getRoles());
            }
        }

        return $this->roles;
    }

    /**
     * @param mixed $object
     * @param string $property
     * @param string $action
     * @return Role|null
     */
    public function getRole($object, $property = '', $action = '')
    {
        foreach ($this->roleProviders as $roleProvider) {
            if ($role = $roleProvider->getRole($object, $property, $action)) {
                return $role;
            }
        }

        return null;
    }

    /**
     * @param RoleProviderInterface $roleProvider
     */
    public function addRoleProvider(RoleProviderInterface $roleProvider)
    {
        $this->roleProviders[] = $roleProvider;
    }
}