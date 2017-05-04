<?php

namespace Imatic\Bundle\UserBundle\Tests\Unit\Monolog;

use Imatic\Bundle\UserBundle\Entity\Group;
use Imatic\Bundle\UserBundle\Entity\User;
use Imatic\Bundle\UserBundle\Monolog\LoggedInUserProcessor;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class LoggedInUserProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider processRecordDataProvider
     */
    public function testProcessRecord(TokenStorageInterface $tokenStorage, array $record, array $expectedRecord)
    {
        $this->assertEquals(
            $expectedRecord,
            (new LoggedInUserProcessor($tokenStorage))->processRecord($record)
        );
    }

    public function processRecordDataProvider()
    {
        return [
            'storage without token' => [
                new TokenStorage(),
                [],
                [],
            ],
            'string user' => [
                $this->createTokenWithUser('string-user'),
                [],
                ['context' => ['user' => ['string' => 'string-user']]],
            ],
            'user implementing UserInterface' => [
                $this->createTokenWithUser((new User())->setUsername('username-is-this')),
                [],
                ['context' => ['user' => ['username' => 'username-is-this']]],
            ],
            'user having __toString method' => [
                $this->createTokenWithUser(new Group('group-name')),
                [],
                ['context' => ['user' => ['string' => 'group-name']]],
            ],
        ];
    }

    /**
     * @param mixed $user
     *
     * @return TokenStorageInterface
     */
    private function createTokenWithUser($user)
    {
        $tokenStorage = new TokenStorage();
        $tokenStorage->setToken(new UsernamePasswordToken($user, 'credentials', 'provider-key'));

        return $tokenStorage;
    }
}
