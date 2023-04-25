<?php declare(strict_types=1);

namespace Imatic\Bundle\UserBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Imatic\Bundle\UserBundle\Model\GroupInterface;
use Imatic\Bundle\UserBundle\Model\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User.
 *
 * @author Viliam Husár <viliam.husar@imatic.cz>
 *
 * @DoctrineAssert\UniqueEntity(fields="usernameCanonical", errorPath="username", groups={"Registration", "Profile"})
 * @DoctrineAssert\UniqueEntity(fields="emailCanonical", errorPath="email", groups={"Registration", "Profile"})
 */
#[ORM\MappedSuperclass()]
class User implements UserInterface, \Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface
{
    #[
        ORM\Id(),
        ORM\Column(
            type: 'integer',
        ),
        ORM\GeneratedValue(
            strategy: 'AUTO',
        ),
    ]
    protected int $id;

    #[
        ORM\Column(
            type: 'string',
            length: 255,
        ),
        Assert\NotBlank(
            groups: [
                'Registration',
                'Profile',
            ],
        ),
        Assert\Length(
            min: 2,
            max: 255,
            groups: [
                'Registration',
                'Profile',
            ],
        )
    ]
    protected string $username;

    #[ORM\Column(
        type: 'string',
        length: 255,
        unique: true,
    )]
    protected string $usernameCanonical;

    #[
        ORM\Column(
            type: 'string',
            length: 255,
        ),
        Assert\NotBlank(
            groups: [
                'Registration',
                'Profile',
            ],
        ),
        Assert\Length(
            min: 2,
            max: 254,
            groups: [
                'Registration',
                'Profile',
            ],
        ),
        Assert\Email(
            groups: [
                'Registration',
                'Profile',
            ],
        )
    ]
    protected string $email;

    #[ORM\Column(
        type: 'string',
        length: 255,
        unique: true,
    )]
    protected string $emailCanonical;

    #[ORM\Column(
        type: 'string',
    )]
    protected string $password;

    /**
     * Plain password, Used for model validation, must not be persisted.
     */
    #[
        Assert\NotBlank(
            groups: [
                'Registration',
                'ResetPassword',
                'ChangePassword',
            ],
        ),
        Assert\Length(
            min: 2,
            groups: [
                'Registration',
                'Profile',
                'ResetPassword',
                'ChangePassword',
            ],
        )
        ]
    protected ?string $plainPassword = null;


    #[ORM\Column(
        type: 'string',
        nullable: true,
    )]
    protected ?string $salt;

    #[ORM\Column(
        type: 'datetime',
        nullable: true,
    )]
    protected ?DateTime $lastLogin = null;

    /**
     * Random string sent to the user email address in order to verify it.
     */
    #[ORM\Column(
        type: 'string',
        nullable: true,
    )]
    protected ?string $confirmationToken;

    #[ORM\Column(
        type: 'datetime',
        nullable: true,
    )]
    protected ?DateTime $passwordRequestedAt;

    #[ORM\Column(
        type: 'boolean',
    )]
    protected bool $enabled;

    #[ORM\Column(
        type: 'boolean',
    )]
    protected bool $locked;

    #[ORM\Column(
        type: 'boolean',
    )]
    protected bool $expired;

    #[ORM\Column(
        type: 'datetime',
        nullable: true,
    )]
    protected ?DateTime $expiresAt;

    #[ORM\Column(
        type: 'boolean',
    )]
    protected bool $credentialsExpired;

    #[ORM\Column(
        type: 'datetime',
        nullable: true,
    )]
    protected ?DateTime $credentialsExpireAt;

    #[ORM\Column(
        type: 'array',
    )]
    protected array $roles;

    #[ORM\ManyToMany(
        targetEntity: GroupInterface::class,
        cascade: ['persist'],
    )]
    protected Collection $groups;

    public function __construct()
    {
        $this->salt = \base_convert(\sha1(\uniqid((string)\mt_rand(), true)), 16, 36);
        $this->enabled = false;
        $this->locked = false;
        $this->expired = false;
        $this->credentialsExpired = false;
        $this->roles = [];
        $this->groups = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string)$this->getUsername();
    }

    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    public function setUsername(string $username): UserInterface
    {
        $this->username = (string)$username;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsernameCanonical(string $usernameCanonical): UserInterface
    {
        $this->usernameCanonical = (string)$usernameCanonical;

        return $this;
    }

    public function getUsernameCanonical(): string
    {
        return $this->usernameCanonical;
    }

    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }

    public function setEmail(string $email): UserInterface
    {
        $this->email = (string)$email;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmailCanonical(string $emailCanonical): UserInterface
    {
        $this->emailCanonical = (string)$emailCanonical;

        return $this;
    }

    public function getEmailCanonical(): string
    {
        return $this->emailCanonical;
    }

    public function setPassword(string $password): UserInterface
    {
        $this->password = (string)$password;

        return $this;
    }

    /**
     * Returns encrypted password.
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }


    public function setPlainPassword(string $password): UserInterface
    {
        $this->plainPassword = (string)$password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setSalt(?string $salt): UserInterface
    {
        $this->salt = $salt;

        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setLastLogin(DateTime $time = null): UserInterface
    {
        $this->lastLogin = $time;

        return $this;
    }

    public function getLastLogin(): ?DateTime
    {
        return $this->lastLogin;
    }

    public function setConfirmationToken(?string $confirmationToken): UserInterface
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setPasswordRequestedAt(DateTime $date = null): UserInterface
    {
        $this->passwordRequestedAt = $date;

        return $this;
    }

    public function getPasswordRequestedAt(): ?DateTime
    {
        return $this->passwordRequestedAt;
    }

    public function setEnabled(bool $boolean): UserInterface
    {
        $this->enabled = (bool)$boolean;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setLocked(bool $bool): UserInterface
    {
        $this->locked = (bool)$bool;

        return $this;
    }

    public function isLocked(): bool
    {
        return !$this->isAccountNonLocked();
    }

    public function setExpired(bool $bool): User
    {
        $this->expired = (bool)$bool;

        return $this;
    }

    public function isExpired(): bool
    {
        return !$this->isAccountNonExpired();
    }

    public function setExpiresAt(DateTime $date = null): UserInterface
    {
        $this->expiresAt = $date;

        return $this;
    }

    public function setCredentialsExpireAt(DateTime $date = null): UserInterface
    {
        $this->credentialsExpireAt = $date;

        return $this;
    }

    public function setCredentialsExpired(bool $bool): UserInterface
    {
        $this->credentialsExpired = (bool)$bool;

        return $this;
    }

    public function setRoles(array $roles): UserInterface
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    public function addRole(string $role): UserInterface
    {
        $role = (string)$role;
        $role = \strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(string $role): UserInterface
    {
        $role = (string)$role;

        if (false !== $key = \array_search(\strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = \array_values($this->roles);
        }

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        foreach ($this->getGroups() as $group) {
            $roles = \array_merge($roles, $group->getRoles());
        }

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return \array_unique($roles);
    }

    /**
     * Returns true if user has role.
     *
     * Never use this to check if this user has access to anything!
     * Use the SecurityContext, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $securityContext->isGranted('ROLE_USER');
     */
    public function hasRole(string $role): bool
    {
        $role = (string)$role;

        return \in_array(\strtoupper($role), $this->getRoles(), true);
    }

    public function addGroup(GroupInterface $group): UserInterface
    {
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }

        return $this;
    }

    public function removeGroup(GroupInterface $group): UserInterface
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->removeElement($group);
        }

        return $this;
    }

    /**
     * @return Collection|GroupInterface[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function getGroupNames(): array
    {
        $names = [];
        foreach ($this->getGroups() as $group) {
            $names[] = $group->getName();
        }

        return $names;
    }

    public function hasGroup(string $name): bool
    {
        return \in_array($name, $this->getGroupNames(), true);
    }

    public function setSuperAdmin(bool $bool): UserInterface
    {
        $bool = (bool)$bool;

        if (true === $bool) {
            $this->addRole(static::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(static::ROLE_SUPER_ADMIN);
        }

        return $this;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }

    public function isAccountNonExpired(): bool
    {
        if (true === $this->expired) {
            return false;
        }

        if (null !== $this->expiresAt && $this->expiresAt->getTimestamp() < \time()) {
            return false;
        }

        return true;
    }

    public function isAccountNonLocked(): bool
    {
        return !$this->locked;
    }

    public function isCredentialsNonExpired(): bool
    {
        if (true === $this->credentialsExpired) {
            return false;
        }

        if (null !== $this->credentialsExpireAt && $this->credentialsExpireAt->getTimestamp() < \time()) {
            return false;
        }

        return true;
    }

    public function isCredentialsExpired(): bool
    {
        return !$this->isCredentialsNonExpired();
    }

    public function isPasswordRequestNonExpired(int $ttl): bool
    {
        return $this->getPasswordRequestedAt() instanceof DateTime &&
            $this->getPasswordRequestedAt()->getTimestamp() + $ttl > \time();
    }

    /**
     * Returns true if same user, false otherwise.
     */
    public function isUser(UserInterface $user = null): bool
    {
        return null !== $user && $this->getId() === $user->getId();
    }

    /**
     * Removes sensitive data from the user.
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * Serializes the user.
     *
     * The serialized data have to contain the fields used by the equals method and the username.
     */
    public function serialize(): string
    {
        return \serialize([
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->expired,
            $this->locked,
            $this->credentialsExpired,
            $this->enabled,
            $this->id,
        ]);
    }

    public function unserialize(string $serialized): void
    {
        $data = \unserialize($serialized);
        // add a few extra elements in the array to ensure that we have enough keys when unserializing
        // older data which does not include all properties.
        $data = \array_merge($data, \array_fill(0, 2, null));

        list(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->expired,
            $this->locked,
            $this->credentialsExpired,
            $this->enabled,
            $this->id
            ) = $data;
    }
}
