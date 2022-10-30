<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Tests\Fixtures\TestProject\ImaticUserBundle\Security\Role\Provider;

use Imatic\Bundle\UserBundle\Security\Role\Provider\RoleProviderInterface;
use Imatic\Bundle\UserBundle\Tests\Fixtures\TestProject\ImaticUserBundle\Security\Role\Role;

class RoleProvider implements RoleProviderInterface
{
    public function getRoles(): array
    {
        $names = $this->getRoleNames();

        $roles = [];
        foreach ($names as $name) {
            $roles[] = new Role($name);
        }

        return $roles;
    }

    protected function getRoleNames(): array
    {
        return [
            'APP_USER_BUNDLE_CREATE',
            'APP_USER_BUNDLE_REMOVE',
            'APP_USER_BUNDLE_UPDATE',
            'APP_USER_BUNDLE_ADMIN',
            'APP_USER_BUNDLE_TESTER',
        ];
    }
}
