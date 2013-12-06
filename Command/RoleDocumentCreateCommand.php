<?php

namespace Imatic\Bundle\UserBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Imatic\Bundle\UserBundle\RoleDocument\RoleDocumentWriter;

/**
 * Role document create command
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class RoleDocumentCreateCommand extends AbstractRoleDocumentCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('imatic:user:role-document:create')
            ->setDescription('Creates role document.')
            ->addOption('user', 'u', InputOption::VALUE_OPTIONAL, 'name of the user to load roles from')
            ->addOption('group', 'g', InputOption::VALUE_OPTIONAL, 'name of the group to load roles from')
            ->addOption('default-state', 'd', InputOption::VALUE_OPTIONAL, 'default role state (1 or 0)')
            ->addArgument('path', InputArgument::OPTIONAL, 'path where to save the resulting document (directory or file.xlsx)');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->checkPhpExcel($output);

        $userName = $input->getOption('user');
        $groupName = $input->getOption('group');
        $defaultState = $input->getOption('default-state');
        if (null !== $defaultState) {
            $defaultState = (bool) $defaultState;
        }

        $roleProvider = $this->getContainer()->get('imatic_user.role_provider');
        $roleTranslator = $this->getContainer()->get('imatic_user.security.role.translation.role_translator');
        $defaultRoles = $this->getDefaultRoles($userName, $groupName, $defaultState);

        $documentWriter = new RoleDocumentWriter($roleProvider, $roleTranslator, $defaultRoles);
        $filePath = $documentWriter->write($input->getArgument('path') ?: getcwd());

        $output->writeln('<info>Success!</info>');
        $output->writeln(sprintf('Saved to <comment>%s</comment>', $filePath));
    }

    /**
     * Get default roles
     *
     * @param string|null $userName
     * @param string|null $groupName
     * @param bool|null   $defaultState
     * @throws \LogicException
     * @return array|bool
     */
    private function getDefaultRoles($userName, $groupName, $defaultState)
    {
        if ($userName xor $groupName) {
            if (null !== $defaultState) {
                throw new \LogicException('Cannot specify default state when user or group name is specified');
            }

            if ($userName) {
                return $this->getUserRoles($userName);
            } else {
                return $this->getGroupRoles($groupName);
            }
        } elseif ($userName && $groupName) {
            throw new \LogicException('You must specify either user or group');
        } else {
            return (bool) $defaultState;
        }
    }

    /**
     * Get user roles
     *
     * @param string $userName
     * @throws \RuntimeException
     * @return array
     */
    private function getUserRoles($userName)
    {
        $userManager = $this->getContainer()->get('fos_user.user_manager');

        $user = $userManager->findUserBy(array('username' => $userName));
        if (!$user) {
            throw new \RuntimeException('User not found');
        }

        return $user->getRoles();
    }

    /**
     * Get group roles
     *
     * @param string $groupName
     * @throws \RuntimeException
     * @return array
     */
    private function getGroupRoles($groupName)
    {
        $groupManager = $this->getContainer()->get('fos_user.group_manager');

        $group = $groupManager->findGroupBy(array('name' => $groupName));
        if (!$group) {
            throw new \RuntimeException('Group not found');
        }

        return $group->getRoles();
    }
}
