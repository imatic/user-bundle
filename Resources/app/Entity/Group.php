<?php

namespace App\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Imatic\Bundle\UserBundle\Entity\Group as BaseGroup;

/**
 * Group
 *
 * @ORM\Entity()
 * @ORM\Table(name="user_group")
 */
class Group extends BaseGroup
{
}
