<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Data\Handler\User;

use Imatic\Bundle\DataBundle\Data\Command\CommandInterface;
use Imatic\Bundle\DataBundle\Data\Command\CommandResultInterface;
use Imatic\Bundle\DataBundle\Data\Command\HandlerInterface;
use Imatic\Bundle\UserBundle\Manager\UserManager;
use Imatic\Bundle\UserBundle\Model\UserInterface;

class UserDeleteHandler implements HandlerInterface
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
        $user = $command->getParameter('user');
        if (!($user instanceof UserInterface)) {
            $user = $this->userManager->findUserBy(['id' => $user]);
        }

        $this->userManager->deleteUser($user);
    }
}
