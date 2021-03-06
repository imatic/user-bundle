<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Command;

use Imatic\Bundle\UserBundle\RoleDocument\RoleDocumentReader;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Role document import command.
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class RoleDocumentImportCommand extends AbstractRoleDocumentCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
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
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
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

                $output->writeln(\sprintf(
                    '<info>Set %s roles for user "%s"</info>',
                    \count($roles),
                    $userName
                ));
            } else {
                $this->applyRolesToGroup($groupName, $roles);

                $output->writeln(\sprintf(
                    '<info>Set %s roles for group "%s"</info>',
                    \count($roles),
                    $groupName
                ));
            }
        } else {
            throw new \LogicException('You must specify either user or group');
        }
    }

    /**
     * Apply roles to user.
     *
     * @param string $userName
     * @param array  $roles
     *
     * @throws \RuntimeException
     */
    private function applyRolesToUser($userName, array $roles): void
    {
        $userManager = $this->getContainer()->get('fos_user.user_manager');

        $user = $userManager->findUserBy(['username' => $userName]);
        if (!$user) {
            throw new \RuntimeException('User not found');
        }

        $user->setRoles($roles);
        $userManager->updateUser($user);
    }

    /**
     * Apply roles to group.
     *
     * @param string $groupName
     * @param array  $roles
     *
     * @throws \RuntimeException
     */
    private function applyRolesToGroup($groupName, array $roles): void
    {
        $groupManager = $this->getContainer()->get('fos_user.group_manager');

        $group = $groupManager->findGroupBy(['name' => $groupName]);
        if (!$group) {
            throw new \RuntimeException('Group not found');
        }

        $group->setRoles($roles);
        $groupManager->updateGroup($group);
    }
}
