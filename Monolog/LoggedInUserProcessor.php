<?php
namespace Imatic\Bundle\UserBundle\Monolog;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class LoggedInUserProcessor
{
    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function processRecord(array $record)
    {
        if (!empty($record['context']['user'])) {
            return $record;
        }

        if (!$token = $this->tokenStorage->getToken()) {
            return $record;
        }

        $user = $token->getUser();
        if ($user instanceof UserInterface) {
            $record['context']['user']['username'] = $user->getUsername();
        } else {
            $record['context']['user']['string'] = (string) $user;
        }

        return $record;
    }
}
