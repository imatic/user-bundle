<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Data\Handler\User;

use Imatic\Bundle\DataBundle\Data\Command\CommandInterface;
use Imatic\Bundle\DataBundle\Data\Command\HandlerInterface;
use Imatic\Bundle\UserBundle\Manager\UserManager;

class UserCreateHandler implements HandlerInterface
{
    private UserManager $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function handle(CommandInterface $command): void
    {
        $user = $command->getParameter('data');

        $this->userManager->updateCanonicalFields($user);
        $this->userManager->updatePassword($user);

        $this->userManager->updateUser($user);
    }
}
