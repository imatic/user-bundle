<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Command;

use Imatic\Bundle\UserBundle\Manager\GroupManager;
use Imatic\Bundle\UserBundle\Manager\UserManager;
use Imatic\Bundle\UserBundle\RoleDocument\RoleDocumentWriter;
use Imatic\Bundle\UserBundle\Security\Role\Provider\RoleProviderInterface;
use Imatic\Bundle\UserBundle\Security\Role\Translation\RoleTranslator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Role document create command.
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class RoleDocumentCreateCommand extends Command
{
    private UserManager $userManager;
    private GroupManager $groupManager;
    private RoleProviderInterface $roleProvider;
    private RoleTranslator $roleTranslator;

    public function __construct(
        UserManager $userManager,
        GroupManager $groupManager,
        RoleProviderInterface $roleProvider,
        RoleTranslator $roleTranslator
    ) {
        parent::__construct();
        $this->userManager = $userManager;
        $this->groupManager = $groupManager;
        $this->roleProvider = $roleProvider;
        $this->roleTranslator = $roleTranslator;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
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
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        CommandUtil::checkPhpExcel($output);

        $userName = $input->getOption('user');
        $groupName = $input->getOption('group');
        $defaultState = $input->getOption('default-state');
        if (null !== $defaultState) {
            $defaultState = (bool) $defaultState;
        }

        $defaultRoles = $this->getDefaultRoles($userName, $groupName, $defaultState);

        $documentWriter = new RoleDocumentWriter($this->roleProvider, $this->roleTranslator, $defaultRoles);
        $filePath = $documentWriter->write($input->getArgument('path') ?: \getcwd());

        $output->writeln('<info>Success!</info>');
        $output->writeln(\sprintf('Saved to <comment>%s</comment>', $filePath));

        return 0;
    }

    /**
     * @throws \LogicException
     */
    private function getDefaultRoles(?string $userName, ?string $groupName, ?bool $defaultState): array|bool
    {
        if ($userName xor $groupName) {
            if (null !== $defaultState) {
                throw new \LogicException('Cannot specify default state when user or group name is specified');
            }

            if ($userName) {
                return $this->getUserRoles($userName);
            }
            return $this->getGroupRoles($groupName);
        } elseif ($userName && $groupName) {
            throw new \LogicException('You must specify either user or group');
        }
        return (bool) $defaultState;
    }

    /**
     * @throws \RuntimeException
     */
    private function getUserRoles(string $userName): array
    {
        $user = $this->userManager->findUserBy(['username' => $userName]);
        if (!$user) {
            throw new \RuntimeException('User not found');
        }

        return $user->getRoles();
    }

    /**
     * @throws \RuntimeException
     */
    private function getGroupRoles(string $groupName): array
    {
        $group = $this->groupManager->findGroupBy(['name' => $groupName]);
        if (!$group) {
            throw new \RuntimeException('Group not found');
        }

        return $group->getRoles();
    }
}
