<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Abstract role document command.
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
abstract class AbstractRoleDocumentCommand extends ContainerAwareCommand
{
    /**
     * Ensure that PHPExcel is available.
     *
     * @param OutputInterface $output
     *
     * @throws \RuntimeException
     */
    protected function checkPhpExcel(OutputInterface $output): void
    {
        if (!\class_exists('PHPExcel')) {
            $output->writeln("\n<error>PHPExcel is not available.</error>\n");
            $output->writeln(
                <<<'COMMENT'
<comment>
composer.json:

    require: {
        ...
        "phpoffice/phpexcel": "1.7.9"
</comment>
COMMENT
            );

            throw new \RuntimeException('PHPExcel is not available');
        }
    }
}
