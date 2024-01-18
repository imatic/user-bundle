<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Model;

use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

/**
 * User interface.
 */
interface UserInterface extends BaseUserInterface
{
    public const ROLE_DEFAULT = 'ROLE_USER';
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    public function getId(): ?int;

    public function setUsername(string $username): self;

    public function getUsernameCanonical(): string;

    public function setUsernameCanonical(string $usernameCanonical): self;

    public function setSalt(?string $salt): self;

    public function getEmail(): ?string;

    public function setEmail(string $email): self;

    public function getEmailCanonical(): string;

    public function setEmailCanonical(string $emailCanonical): self;

    public function getPlainPassword():?string;

    public function setPlainPassword(string $password): self;

    public function setPassword(string $password): self;

    public function isSuperAdmin(): bool;

    public function setEnabled(bool $boolean): self;

    public function setSuperAdmin(bool $boolean): self;

    public function getConfirmationToken():?string;

    public function setConfirmationToken(?string $confirmationToken): self;

    public function setPasswordRequestedAt(\DateTime $date = null): self;

    public function isPasswordRequestNonExpired(int $ttl): bool;

    public function setLastLogin(\DateTime $time = null): self;

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the AuthorizationChecker, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $authorizationChecker->isGranted('ROLE_USER');
     */
    public function hasRole(string $role):bool;

    /**
     * Sets the roles of the user.
     *
     * This overwrites any previous roles.
     *
     */
    public function setRoles(array $roles): self;

    public function addRole(string $role): self;

    public function removeRole(string $role): self;

    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return bool true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired(): bool;

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return bool true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked(): bool;

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return bool true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired(): bool;

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @return bool true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled(): bool;
}
