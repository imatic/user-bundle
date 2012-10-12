<?php

namespace Imatic\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;
use FOS\UserBundle\Entity\Group as BaseGroup;

/**
 * Imatic\Bundle\UserBundle\Entity\Group
 *
 * @ORM\Entity()
 * @ORM\Table(name="imatic_user_group")
 * @DoctrineAssert\UniqueEntity("name")
 * @DoctrineAssert\UniqueEntity("description")
 */
class Group extends BaseGroup
{
    /**
     * @var integer $id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     */
    protected $id;

    /**
     * @var string $description
     *
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     * @Assert\MaxLength(255)
     */
    protected $description;

    /**
     * @param string $name
     * @param array $roles
     */
    public function __construct($name = null, $roles = array())
    {
        parent::__construct($name, $roles);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getName();
    }
}