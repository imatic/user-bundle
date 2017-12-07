<?php
namespace AppUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Imatic\Bundle\UserBundle\Entity\Group as BaseGroup;

/**
 * Group.
 *
 * @ORM\Entity()
 * @ORM\Table()
 */
class Group extends BaseGroup
{
}
