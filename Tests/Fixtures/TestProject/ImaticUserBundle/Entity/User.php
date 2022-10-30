<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Tests\Fixtures\TestProject\ImaticUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Imatic\Bundle\UserBundle\Entity\User as BaseUser;

#[
    ORM\Entity(),
    ORM\Table(
        name: 'User',
    ),
]
class User extends BaseUser
{
    public function setSalt($salt): \Imatic\Bundle\UserBundle\Model\UserInterface
    {
        $this->salt = $salt;
    }
}
