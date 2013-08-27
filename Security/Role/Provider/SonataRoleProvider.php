<?php
namespace Imatic\Bundle\UserBundle\Security\Role\Provider;

use Imatic\Bundle\UserBundle\Security\Role\SonataRole;
use Imatic\Bundle\UserBundle\Security\Role\SonataRoleFactory;
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
            $roleFactory = new SonataRoleFactory();

            foreach ($this->pool->getAdminServiceIds() as $id) {
                try {
                    $admin = $this->pool->getInstance($id);
                } catch (ExceptionInterface $e) {
                    continue;
                }

                $baseRole = $admin->getSecurityHandler()->getBaseRole($admin);

                foreach (array_keys($admin->getSecurityInformation()) as $action) {
                    $role = sprintf($baseRole, $action);
                    $this->roles[] = $roleFactory->createRole($admin, $action, $role);
                }
            }
        }

        return $this->roles;
    }
}