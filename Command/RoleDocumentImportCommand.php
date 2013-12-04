<?php

namespace Imatic\Bundle\UserBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Imatic\Bundle\UserBundle\RoleDocument\RoleDocumentReader;

/**
 * Role document import command
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class RoleDocumentImportCommand extends AbstractRoleDocumentCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('imatic:user:role-document:import')
            ->setDescription('Imports role document.')
            ->addOption('list', 'l', InputOption::VALUE_NONE, 'list roles only (do not apply)')
            ->addOption('user', 'u', InputOption::VALUE_OPTIONAL, 'name of the user to apply the roles to')
            ->addOption('group', 'g', InputOption::VALUE_OPTIONAL, 'name of the group to apply the roles to')
            ->addArgument('path', InputArgument::REQUIRED, 'path to the document');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->checkPhpExcel($output);

        $path = $input->getArgument('path');
        $userName = $input->getOption('user');
        $groupName = $input->getOption('group');
        
        $reader = new RoleDocumentReader();
        $reader->open($path);
        $roles = $reader->readEnabledRoles();
        
        if ($input->getOption('list')) {
            // list
            foreach ($roles as $role) {
                $output->writeln($role);
            }
        } elseif ($userName xor $groupName) {
            // apply
            if ($userName) {
                $this->applyRolesToUser($userName, $roles);

                $output->writeln(sprintf(
                    '<info>Set %s roles for user "%s"</info>',
                    sizeof($roles),
                    $userName
                ));
            } else {
                $this->applyRolesToGroup($groupName, $roles);

                $output->writeln(sprintf(
                    '<info>Set %s roles for group "%s"</info>',
                    sizeof($roles),
                    $groupName
                ));
            }
        } else {
            throw new \InvalidArgumentException('You must specify either user or group');
        }
    }

    /**
     * Apply roles to user
     *
     * @param string $userName
     * @param array  $roles
     */
    private function applyRolesToUser($userName, array $roles)
    {
        $userManager = $this->getContainer()->get('fos_user.user_manager');

        $user = $userManager->findUserBy(array('username' => $userName));
        if (!$user) {
            throw new \RuntimeException('User not found');
        }

        $user->setRoles($roles);
        $userManager->updateUser($user);
    }

    /**
     * Apply roles to group
     *
     * @param string $groupName
     * @param array  $roles
     */
    private function applyRolesToGroup($groupName, array $roles)
    {
        $groupManager = $this->getContainer()->get('fos_user.group_manager');

        $group = $groupManager->findGroupBy(array('name' => $groupName));
        if (!$group) {
            throw new \RuntimeException('Group not found');
        }

        $group->setRoles($roles);
        $groupManager->updateGroup($group);
    }
}
