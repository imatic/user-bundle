<?php

namespace Imatic\Bundle\UserBundle\Tests\Fixtures\TestProject\ImaticUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Imatic\Bundle\UserBundle\Entity\User as BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="`User`")
 *
 * @author Miloslav Nenadal <miloslav.nenadal@imatic.cz>
 */
class User extends BaseUser
{
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }
}
