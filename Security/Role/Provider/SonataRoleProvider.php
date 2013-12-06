<?php
namespace Imatic\Bundle\UserBundle\Security\Role\Provider;

use Imatic\Bundle\UserBundle\Security\Role\SonataRole;
use Imatic\Bundle\UserBundle\Security\Role\SonataRoleFactory;
use Sonata\AdminBundle\Admin\Pool;
use Symfony\Component\DependencyInjection\Exception\ExceptionInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

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
                $actions = $this->getAdminActions($admin);

                foreach ($actions as $action) {
                    $role = sprintf($baseRole, $action);
                    $this->roles[] = $roleFactory->createRole($admin, $action, $role);
                }
            }
        }

        return $this->roles;
    }
    
    /**
     * @param AdminInterface $admin
     * @return array
     */
    private function getAdminActions(AdminInterface $admin)
    {
        $order = array(
            'LIST' => 0,
            'VIEW' => 1,
            'CREATE' => 2,
            'EDIT' => 3,
            'DELETE' => 4,
            'EXPORT' => 5,
            'OPERATOR' => 6,
            'MASTER' => 7,
            'ROLES' => 8,
        );

        $actions = array_keys($admin->getSecurityInformation());

        usort($actions, function ($a, $b) use ($order) {
            if (isset($order[$a], $order[$b])) {
                return $order[$a] > $order[$b] ? 1 : -1;
            } else {
                return isset($order[$a]) ? 1 : -1;
            }
        });

        return $actions;
    }
}