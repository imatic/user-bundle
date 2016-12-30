<?php

namespace Imatic\Bundle\UserBundle\Data\Handler\User;

use FOS\UserBundle\Model\UserManagerInterface;
use Imatic\Bundle\DataBundle\Data\Command\CommandInterface;
use Imatic\Bundle\DataBundle\Data\Command\CommandResultInterface;
use Imatic\Bundle\DataBundle\Data\Command\HandlerInterface;

class UserEditHandler implements HandlerInterface
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
        $user = $command->getParameter('data');

        $this->userManager->updateCanonicalFields($user);
        $this->userManager->updatePassword($user);

        $this->userManager->updateUser($user);
    }
}
