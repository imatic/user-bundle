<?php

namespace Imatic\Bundle\UserBundle\Security;

use FOS\UserBundle\Security\EmailUserProvider as BaseEmailUserProvider;
use Imatic\Bundle\UserBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * Provides user by email or username.
 *
 * @author Viliam Husár <viliam.husar@imatic.cz>
 */
class EmailUserProvider extends BaseEmailUserProvider
{
    /**
     * {@inheritdoc}
     */
    public function refreshUser(SecurityUserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Expected an instance of Imatic\Bundle\UserBundle\Entity\User, but got "%s".', get_class($user)));
        }

        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Expected an instance of %s, but got "%s".', $this->userManager->getClass(), get_class($user)));
        }

        if (null === $reloadedUser = $this->userManager->findUserBy(array('id' => $user->getId()))) {
            throw new UsernameNotFoundException(sprintf('User with ID "%d" could not be reloaded.', $user->getId()));
        }

        return $reloadedUser;
    }
}
