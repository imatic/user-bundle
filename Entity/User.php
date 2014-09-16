<?php

namespace Imatic\Bundle\UserBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\UserInterface as BaseUserInterface;
use Imatic\Bundle\UserBundle\Model\GroupInterface;
use Imatic\Bundle\UserBundle\Model\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User.
 *
 * @author Viliam Husár <viliam.husar@imatic.cz>
 *
 * @ORM\MappedSuperclass()
 * @DoctrineAssert\UniqueEntity(fields="usernameCanonical", errorPath="username", message="fos_user.username.already_used",  groups={"Registration", "Profile"})
 * @DoctrineAssert\UniqueEntity(fields="emailCanonical", errorPath="email", message="fos_user.email.already_used",  groups={"Registration", "Profile"})
 */
class User implements UserInterface
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
     * @ORM\Column(type="string", length=255, name="username")
     * @Assert\NotBlank(message="fos_user.username.blank", groups={"Registration", "Profile"})
     * @Assert\Length(min=2, max=255, minMessage="fos_user.username.short", maxMessage="fos_user.username.long", groups={"Registration", "Profile"})
     */
    protected $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true, name="username_canonical")
     */
    protected $usernameCanonical;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, name="email")
     * @Assert\NotBlank(message="fos_user.email.blank", groups={"Registration", "Profile"})
     * @Assert\Length(min=2, max=254, minMessage="fos_user.email.short", maxMessage="fos_user.email.long", groups={"Registration", "Profile"})
     * @Assert\Email(message="fos_user.email.invalid", groups={"Registration", "Profile"})
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true, name="email_canonical")
     */
    protected $emailCanonical;

    /**
     * Encrypted password
     * @var string
     *
     * @ORM\Column(type="string", name="password")
     */
    protected $password;

    /**
     * Plain password, Used for model validation, must not be persisted
     * @var string
     *
     * @Assert\NotBlank(message="fos_user.password.blank", groups={"Registration", "ResetPassword", "ChangePassword"})
     * @Assert\Length(min=2, minMessage="fos_user.password.short", groups={"Registration", "Profile", "ResetPassword", "ChangePassword"})
     */
    protected $plainPassword;

    /**
     * The salt to use for hashing
     * @var string
     *
     * @ORM\Column(type="string", name="salt")
     */
    protected $salt;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", name="last_login", nullable=true)
     */
    protected $lastLogin;

    /**
     * Random string sent to the user email address in order to verify it
     * @var string
     *
     * @ORM\Column(type="string", nullable=true, name="confirmation_token")
     */
    protected $confirmationToken;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", name="password_requested_at", nullable=true)
     */
    protected $passwordRequestedAt;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="enabled")
     */
    protected $enabled;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="locked")
     */
    protected $locked;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="expired")
     */
    protected $expired;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", name="expires_at", nullable=true)
     */
    protected $expiresAt;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="credentials_expired")
     */
    protected $credentialsExpired;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", name="credentials_expired_at", nullable=true)
     */
    protected $credentialsExpireAt;

    /**
     * @var array
     *
     * @ORM\Column(type="array", name="roles")
     */
    protected $roles;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Imatic\Bundle\UserBundle\Model\GroupInterface", cascade={"persist", "remove"})
     */
    protected $groups;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->enabled = false;
        $this->locked = false;
        $this->expired = false;
        $this->credentialsExpired = false;
        $this->roles = array();
        $this->groups = new ArrayCollection();
    }

    /**
     * Returns string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getUsername();
    }


    /**
     * Returns id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets username.
     *
     * @param string $username
     *
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = (string) $username;

        return $this;
    }

    /**
     * Returns username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Sets canonical username.
     *
     * @param string $usernameCanonical
     *
     * @return $this
     */
    public function setUsernameCanonical($usernameCanonical)
    {
        $this->usernameCanonical = (string) $usernameCanonical;

        return $this;
    }

    /**
     * Returns canonical username.
     *
     * @return string
     */
    public function getUsernameCanonical()
    {
        return $this->usernameCanonical;
    }

    /**
     * Sets email.
     *
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = (string) $email;

        return $this;
    }

    /**
     * Returns email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets canonical email.
     *
     * @param string $emailCanonical
     *
     * @return $this
     */
    public function setEmailCanonical($emailCanonical)
    {
        $this->emailCanonical = (string) $emailCanonical;

        return $this;
    }

    /**
     * Returns canonical email.
     *
     * @return string
     */
    public function getEmailCanonical()
    {
        return $this->emailCanonical;
    }

    /**
     * Sets encrypted password.
     *
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = (string) $password;

        return $this;
    }

    /**
     * Returns encrypted password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets plain password.
     *
     * @param string $password
     *
     * @return $this
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = (string) $password;

        return $this;
    }

    /**
     * Returns plain password.
     *
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Returns salt.
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Sets last login.
     *
     * @param DateTime $time
     *
     * @return $this
     */
    public function setLastLogin(DateTime $time = null)
    {
        $this->lastLogin = $time;

        return $this;
    }

    /**
     * Returns last login time.
     *
     * @return DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Sets confirmation token.
     *
     * @param string $confirmationToken
     *
     * @return $this
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * Returns confirmation token.
     *
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * Sets password request at.
     *
     * @param DateTime $date
     *
     * @return $this
     */
    public function setPasswordRequestedAt(DateTime $date = null)
    {
        $this->passwordRequestedAt = $date;

        return $this;
    }

    /**
     * Returns password requested at.
     *
     * @return null|\DateTime
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    /**
     * Sets enabled.
     *
     * @param bool $bool
     *
     * @return $this
     */
    public function setEnabled($bool)
    {
        $this->enabled = (bool) $bool;

        return $this;
    }

    /**
     * Returns true if user is enabled, false otherwise.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Sets locked.
     *
     * @param bool $bool
     *
     * @return $this
     */
    public function setLocked($bool)
    {
        $this->locked = (bool) $bool;

        return $this;
    }

    /**
     * Returns true if user is locked, false otherwise.
     *
     * @return bool
     */
    public function isLocked()
    {
        return !$this->isAccountNonLocked();
    }

    /**
     * Sets expired.
     *
     * @param bool $bool
     *
     * @return User
     */
    public function setExpired($bool)
    {
        $this->expired = (bool) $bool;

        return $this;
    }

    /**
     * Returns true if user is expired, false otherwise.
     *
     * @return bool
     */
    public function isExpired()
    {
        return !$this->isAccountNonExpired();
    }

    /**
     * Sets expired at.
     *
     * @param DateTime $date
     *
     * @return $this
     */
    public function setExpiresAt(DateTime $date = null)
    {
        $this->expiresAt = $date;

        return $this;
    }

    /**
     * Sets credential expired at.
     *
     * @param DateTime $date
     *
     * @return $this
     */
    public function setCredentialsExpireAt(DateTime $date = null)
    {
        $this->credentialsExpireAt = $date;

        return $this;
    }

    /**
     * Sets credentials expired.
     *
     * @param bool $bool
     *
     * @return $this
     */
    public function setCredentialsExpired($bool)
    {
        $this->credentialsExpired = (bool) $bool;

        return $this;
    }

    /**
     * Sets roles.
     *
     * @param array $roles
     *
     * @return $this
     */
    public function setRoles(array $roles)
    {
        $this->roles = array();

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * Adds role.
     *
     * @param string $role
     *
     * @return $this
     */
    public function addRole($role)
    {
        $role = (string) $role;
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * Removes role.
     *
     * @param string $role
     *
     * @return $this
     */
    public function removeRole($role)
    {
        $role = (string) $role;

        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * Returns roles.
     *
     * @return array
     */
    public function getRoles()
    {
        $roles = $this->roles;

        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }

    /**
     * Returns true if user has role.
     *
     * Never use this to check if this user has access to anything!
     * Use the SecurityContext, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $securityContext->isGranted('ROLE_USER');
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        $role = (string) $role;

        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * Adds group.
     *
     * @param GroupInterface $group
     *
     * @return $this
     */
    public function addGroup(GroupInterface $group)
    {
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }

        return $this;
    }

    /**
     * Removes group.
     *
     * @param GroupInterface $group
     *
     * @return $this
     */
    public function removeGroup(GroupInterface $group)
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->removeElement($group);
        }

        return $this;
    }

    /**
     * Returns groups.
     *
     * @return Collection|GroupInterface[]
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Returns group names.
     *
     * @return array
     */
    public function getGroupNames()
    {
        $names = array();
        foreach ($this->getGroups() as $group) {
            $names[] = $group->getName();
        }

        return $names;
    }

    /**
     * Returns true if user has given group, false otherwise.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasGroup($name)
    {
        return in_array($name, $this->getGroupNames());
    }

    /**
     * Sets super admin.
     *
     * @param bool $bool
     *
     * @return $this
     */
    public function setSuperAdmin($bool)
    {
        $bool = (bool) $bool;

        if (true === $bool) {
            $this->addRole(static::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(static::ROLE_SUPER_ADMIN);
        }

        return $this;
    }

    /**
     * Returns true if user is super admin, false otherwise.
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }

    /**
     * Returns true if user account is not expired, false otherwsie.
     *
     * @return bool
     */
    public function isAccountNonExpired()
    {
        if (true === $this->expired) {
            return false;
        }

        if (null !== $this->expiresAt && $this->expiresAt->getTimestamp() < time()) {
            return false;
        }

        return true;
    }

    /**
     * Returns true if account is not locked, false otherwise.
     *
     * @return bool
     */
    public function isAccountNonLocked()
    {
        return !$this->locked;
    }

    /**
     * Returns true if user credentials are not expired, false otherwise.
     *
     * @return bool
     */
    public function isCredentialsNonExpired()
    {
        if (true === $this->credentialsExpired) {
            return false;
        }

        if (null !== $this->credentialsExpireAt && $this->credentialsExpireAt->getTimestamp() < time()) {
            return false;
        }

        return true;
    }

    /**
     * Returns true if user credentials expired, false otherwise.
     *
     * @return bool
     */
    public function isCredentialsExpired()
    {
        return !$this->isCredentialsNonExpired();
    }

    /**
     * Returns true if password request is not expired.
     *
     * @param int $ttl
     *
     * @return bool
     */
    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->getPasswordRequestedAt() instanceof DateTime &&
        $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    /**
     * Returns true if same user, false otherwise.
     *
     * @param BaseUserInterface $user
     *
     * @return bool
     */
    public function isUser(BaseUserInterface $user = null)
    {
        return null !== $user && $this->getId() === $user->getId();
    }

    /**
     * Removes sensitive data from the user.
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * Serializes the user.
     *
     * The serialized data have to contain the fields used by the equals method and the username.
     *
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
                $this->password,
                $this->salt,
                $this->usernameCanonical,
                $this->username,
                $this->expired,
                $this->locked,
                $this->credentialsExpired,
                $this->enabled,
                $this->id,
            ));
    }

    /**
     * Unserializes the user.
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        // add a few extra elements in the array to ensure that we have enough keys when unserializing
        // older data which does not include all properties.
        $data = array_merge($data, array_fill(0, 2, null));

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
