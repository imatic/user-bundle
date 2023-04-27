<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Tests\Unit\Monolog;

use Imatic\Bundle\UserBundle\Entity\User;
use Imatic\Bundle\UserBundle\Monolog\LoggedInUserProcessor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class LoggedInUserProcessorTest extends TestCase
{
    /**
     * @dataProvider processRecordDataProvider
     */
    public function testProcessRecord(TokenStorageInterface $tokenStorage, array $record, array $expectedRecord): void
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
            'user implementing UserInterface' => [
                $this->createTokenWithUser((new User())->setUsername('username-is-this')),
                [],
                ['context' => ['user' => ['username' => 'username-is-this']]],
            ],
        ];
    }

    private function createTokenWithUser(mixed $user): TokenStorageInterface
    {
        $tokenStorage = new TokenStorage();
        $tokenStorage->setToken(new UsernamePasswordToken($user, 'credentials', []));

        return $tokenStorage;
    }
}
