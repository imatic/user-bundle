<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Data\Handler\User;

use Imatic\Bundle\DataBundle\Data\Command\CommandInterface;
use Imatic\Bundle\DataBundle\Data\Command\CommandResultInterface;
use Imatic\Bundle\DataBundle\Data\Command\HandlerInterface;
use Imatic\Bundle\UserBundle\Manager\UserManager;

class UserEditHandler implements HandlerInterface
{
    private UserManager $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @param CommandInterface $command
     *
     * @return CommandResultInterface|bool|void
     */
    public function handle(CommandInterface $command)
    {
        $user = $command->getParameter('data');

        $this->userManager->updateCanonicalFields($user);
        $this->userManager->updatePassword($user);

        $this->userManager->updateUser($user);
    }
}
