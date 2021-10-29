<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Imatic\Bundle\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\SelfSaltingEncoderInterface;

class UserManager
{
    private EntityManagerInterface $em;
    private EncoderFactoryInterface $encoderFactory;
    private string $userClass;

    public function __construct(EntityManagerInterface $om, EncoderFactoryInterface $encoderFactory, string $userClass)
    {
        $this->em = $om;
        $this->encoderFactory = $encoderFactory;
        $this->userClass = $userClass;
    }

    private function getRepository(): ObjectRepository
    {
        return $this->em->getRepository($this->userClass);
    }

    public function findUserByEmail(string $email)
    {
        return $this->findUserBy(['emailCanonical' => $this->canonicalize($email)]);
    }

    public function findUserByUsername(string $username)
    {
        return $this->findUserBy(['usernameCanonical' => $this->canonicalize($username)]);
    }

    public function findUserByUsernameOrEmail(string $usernameOrEmail)
    {
        if (\preg_match('/^.+\@\S+\.\S+$/', $usernameOrEmail)) {
            $user = $this->findUserByEmail($usernameOrEmail);
            if (null !== $user) {
                return $user;
            }
        }

        return $this->findUserByUsername($usernameOrEmail);
    }

    public function findUserByConfirmationToken($token)
    {
        return $this->findUserBy(['confirmationToken' => $token]);
    }

    public function findUserBy(array $criteria)
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    private function canonicalize(?string $string)
    {
        if (null === $string) {
            return;
        }

        $encoding = \mb_detect_encoding($string, \mb_detect_order(), true);
        $result = $encoding
            ? \mb_convert_case($string, MB_CASE_LOWER, $encoding)
            : \mb_convert_case($string, MB_CASE_LOWER);

        return $result;
    }

    public function updateCanonicalFields(UserInterface $user): void
    {
        $user->setUsernameCanonical($this->canonicalize($user->getUsername()));
        $user->setEmailCanonical($this->canonicalize($user->getEmail()));
    }

    public function updatePassword(UserInterface $user): void
    {
        $plainPassword = $user->getPlainPassword();

        if ($plainPassword === null || 0 === \strlen($plainPassword)) {
            return;
        }

        $encoder = $this->encoderFactory->getEncoder($user);

        if ($encoder instanceof BCryptPasswordEncoder || $encoder instanceof SelfSaltingEncoderInterface) {
            $user->setSalt(null);
        } else {
            $salt = \rtrim(\str_replace('+', '.', \base64_encode(\random_bytes(32))), '=');
            $user->setSalt($salt);
        }

        $hashedPassword = $encoder->encodePassword($plainPassword, $user->getSalt());
        $user->setPassword($hashedPassword);
        $user->eraseCredentials();
    }

    public function updateUser(UserInterface $user, bool $andFlush = true): void
    {
        $this->updateCanonicalFields($user);
        $this->updatePassword($user);

        $this->em->persist($user);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    public function deleteUser(UserInterface $user): void
    {
        $this->em->remove($user);
        $this->em->flush();
    }

    public function getClass()
    {
        return $this->userClass;
    }
}
