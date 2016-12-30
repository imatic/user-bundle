<?php

namespace Imatic\Bundle\UserBundle\Security\Role\Provider;

use Imatic\Bundle\UserBundle\Security\Role\Role;

interface RoleProviderInterface
{
    /**
     * @return Role[]
     */
    public function getRoles();
}
