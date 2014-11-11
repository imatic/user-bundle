<?php

namespace Imatic\Bundle\UserBundle\Tests\Fixtures\TestProject\ImaticUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Imatic\Bundle\UserBundle\Entity\Group as BaseGroup;

/**
 * @ORM\Entity
 * @ORM\Table(name="`Group`")
 *
 * @author Miloslav Nenadal <miloslav.nenadal@imatic.cz>
 */
class Group extends BaseGroup
{

}
