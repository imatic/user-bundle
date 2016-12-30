<?php

namespace Imatic\Bundle\UserBundle\Data\Handler\User;

use FOS\UserBundle\Model\UserManagerInterface;
use Imatic\Bundle\DataBundle\Data\Command\CommandInterface;
use Imatic\Bundle\DataBundle\Data\Command\CommandResultInterface;
use Imatic\Bundle\DataBundle\Data\Command\HandlerInterface;
use Imatic\Bundle\UserBundle\Model\UserInterface;

class UserDeleteHandler implements HandlerInterface
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
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
