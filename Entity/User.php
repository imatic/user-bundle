<?php

namespace Imatic\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use FOS\UserBundle\Entity\User as BaseUser;

/**
 * @ORM\MappedSuperclass
 */
abstract class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Bundle\UserBundle\Entity\Group")
     * @ORM\JoinTable(name="user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $groups;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\MinLength(3)
     * @Assert\MaxLength(255)
     */
    protected $fullname;

    /**
     * @param string $name
     */
    public function setFullname($name)
    {
        $this->fullname = $name;
    }

    /**
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->getFullname()) {
            return (string)$this->getFullname();
        }
        return (string)$this->getUsername();
    }
}
