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
            ->addOption('default-state', 'd', InputOption::VALUE_OPTIONAL, 'default role state (1 or 0)')
            ->addArgument('path', InputArgument::OPTIONAL, 'path where to save the resulting document (directory or file.xlsx)');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->checkPhpExcel($output);

        $defaultState = (bool) $input->getOption('default-state');

        $roleProvider = $this->getContainer()->get('imatic_user.role_provider');
        $roleTranslator = $this->getContainer()->get('imatic_user.security.role.translation.role_translator');

        $documentWriter = new RoleDocumentWriter($roleProvider, $roleTranslator, $defaultState);
        $filePath = $documentWriter->write($input->getArgument('path') ?: getcwd());

        $output->writeln('<info>Success!</info>');
        $output->writeln(sprintf('Saved to <comment>%s</comment>', $filePath));
    }
}
