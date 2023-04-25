<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Tests\Fixtures\Role;

use Imatic\Bundle\UserBundle\Security\Role\Role;

class ParentRole extends Role
{
    public function __construct(
        private string $role
    )
    {
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getType(): string
    {
        return 'type';
    }
}
