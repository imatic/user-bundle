<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Model;

use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

/**
 * User interface.
 *
 * @author Viliam Husár <viliam.husar@imatic.cz>
 */
interface UserInterface extends BaseUserInterface
{
    public const ROLE_DEFAULT = 'ROLE_USER';
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * Returns the user unique id.
     *
     * @return mixed
     */
    public function getId(): int;

    /**
     * Sets the username.
     *
     * @param string $username
     *
     * @return static
     */
    public function setUsername(string $username): UserInterface;

    /**
     * Gets the canonical username in search and sort queries.
     *
     * @return string
     */
    public function getUsernameCanonical(): string;

    /**
     * Sets the canonical username.
     *
     * @param string $usernameCanonical
     *
     * @return static
     */
    public function setUsernameCanonical(string $usernameCanonical): UserInterface;

    public function setSalt(?string $salt): UserInterface;

    /**
     * Gets email.
     *
     * @return string
     */
    public function getEmail(): string;

    /**
     * Sets the email.
     *
     * @param string $email
     *
     * @return static
     */
    public function setEmail(string $email): UserInterface;

    /**
     * Gets the canonical email in search and sort queries.
     *
     * @return string
     */
    public function getEmailCanonical(): string;

    /**
     * Sets the canonical email.
     *
     * @param string $emailCanonical
     *
     * @return static
     */
    public function setEmailCanonical(string $emailCanonical): UserInterface;

    /**
     * Gets the plain password.
     */
    public function getPlainPassword():?string;

    /**
     * Sets the plain password.
     *
     * @param string $password
     *
     * @return static
     */
    public function setPlainPassword(string $password): UserInterface;

    /**
     * Sets the hashed password.
     *
     * @param string $password
     *
     * @return static
     */
    public function setPassword(string $password): UserInterface;

    /**
     * Tells if the the given user has the super admin role.
     *
     * @return bool
     */
    public function isSuperAdmin(): bool;

    /**
     * @param bool $boolean
     *
     * @return static
     */
    public function setEnabled(bool $boolean): UserInterface;

    /**
     * Sets the super admin status.
     *
     * @param bool $boolean
     *
     * @return static
     */
    public function setSuperAdmin(bool $boolean): UserInterface;

    /**
     * Gets the confirmation token.
     *
     * @return string|null
     */
    public function getConfirmationToken():?string;

    /**
     * Sets the confirmation token.
     *
     * @param string|null $confirmationToken
     *
     * @return static
     */
    public function setConfirmationToken(?string $confirmationToken): UserInterface;

    /**
     * Sets the timestamp that the user requested a password reset.
     *
     * @return static
     */
    public function setPasswordRequestedAt(\DateTime $date = null): UserInterface;

    /**
     * Checks whether the password reset request has expired.
     *
     * @param int $ttl Requests older than this many seconds will be considered expired
     *
     * @return bool
     */
    public function isPasswordRequestNonExpired(int $ttl): bool;

    /**
     * Sets the last login time.
     *
     * @return static
     */
    public function setLastLogin(\DateTime $time = null): UserInterface;

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the AuthorizationChecker, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $authorizationChecker->isGranted('ROLE_USER');
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole(string $role):bool;

    /**
     * Sets the roles of the user.
     *
     * This overwrites any previous roles.
     *
     */
    public function setRoles(array $roles): UserInterface;

    /**
     * Adds a role to the user.
     *
     * @param string $role
     *
     * @return static
     */
    public function addRole(string $role): UserInterface;

    /**
     * Removes a role to the user.
     *
     * @param string $role
     *
     * @return static
     */
    public function removeRole(string $role): UserInterface;

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
