<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Imatic\Bundle\UserBundle\Model\GroupInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Group.
 *
 * @author Viliam Husár <viliam.husar@imatic.cz>
 *
 * @ORM\MappedSuperclass()
 */
class Group implements GroupInterface
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank(groups={"Registration"})
     * @Assert\Length(min=2, max=255, groups={"Registration"})
     */
    protected $name;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $roles;

    /**
     * Constructor.
     *
     * @param string $name
     * @param array  $roles
     */
    public function __construct($name = null, $roles = [])
    {
        $this
            ->setName($name)
            ->setRoles($roles);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    public function addRole(string $role): static
    {
        $role = (string) $role;
        $role = \strtoupper($role);

        if (!$this->hasRole($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(string $role): static
    {
        $role = (string) $role;

        if (false !== $key = \array_search(\strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = \array_values($this->roles);
        }

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function hasRole(string $role): bool
    {
        $role = (string) $role;

        return \in_array(\strtoupper($role), $this->roles, true);
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }
}
