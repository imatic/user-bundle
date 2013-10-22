<?php

namespace App\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Imatic\Bundle\UserBundle\Entity\User as BaseUser;

/**
 * User
 *
 * @ORM\Entity
 * @ORM\Table()
 */
class User extends BaseUser
{
}
