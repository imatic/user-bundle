<?php
namespace Imatic\Bundle\UserBundle\Security\Role;

use Sonata\AdminBundle\Admin\Pool;
use Symfony\Component\DependencyInjection\Exception\ExceptionInterface;

class SonataRoleProvider implements RoleProviderInterface
{
    /** @var Pool */
    private $pool;

    /** @var SonataRole[] */
    private $roles;

    /**
     * @param Pool $pool
     */
    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    /**
     * @return SonataRole[]
     */
    public function getRoles()
    {
        if ($this->roles === null) {
            $this->roles = [];

            foreach ($this->pool->getAdminServiceIds() as $id) {
                try {
                    $admin = $this->pool->getInstance($id);
                } catch (ExceptionInterface $e) {
                    continue;
                }

                $baseRole = $admin->getSecurityHandler()->getBaseRole($admin);

                foreach (array_keys($admin->getSecurityInformation()) as $action) {
                    $role = sprintf($baseRole, $action);
                    $roleFactory = new SonataRoleFactory();
                    $this->roles[$role] = $roleFactory->createRole($admin, $action, $role);
                }
            }
        }

        return array_values($this->roles);
    }

    /**
     * @param string $role
     * @param string $property
     * @param string $action
     * @return SonataRole|null
     */
    public function getRole($role, $property = '', $action = '')
    {
        $this->getRoles();

        return isset($this->roles[$role]) ? $this->roles[$role] : null;
    }
}