<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Security;

use Imatic\Bundle\UserBundle\Entity\User;
use Imatic\Bundle\UserBundle\Manager\UserManager;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Provides user by email or username.
 *
 * @author Viliam Husár <viliam.husar@imatic.cz>
 */
class EmailUserProvider implements UserProviderInterface
{
    private UserManager $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(\sprintf('Expected an instance of Imatic\Bundle\UserBundle\Entity\User, but got "%s".', \get_class($user)));
        }

        if (!$this->supportsClass(\get_class($user))) {
            throw new UnsupportedUserException(\sprintf('Expected an instance of %s, but got "%s".', $this->userManager->getClass(), \get_class($user)));
        }

        if (null === $reloadedUser = $this->userManager->findUserBy(['id' => $user->getId()])) {
            throw new UsernameNotFoundException(\sprintf('User with ID "%d" could not be reloaded.', $user->getId()));
        }

        return $reloadedUser;
    }

    public function loadUserByUsername($username): UserInterface
    {
        $user = $this->userManager->findUserByUsernameOrEmail($username);

        if (!$user) {
            throw new UsernameNotFoundException(\sprintf('Username "%s" does not exist.', $username));
        }

        return $user;
    }

    public function supportsClass($class): bool
    {
        $userClass = $this->userManager->getClass();

        return $userClass === $class || \is_subclass_of($class, $userClass);
    }
}
