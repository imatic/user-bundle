<?php declare(strict_types=1);
namespace AppUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Imatic\Bundle\UserBundle\Entity\User as BaseUser;

#[
    ORM\Entity(),
    ORM\Table(),
]
class User extends BaseUser
{
}
