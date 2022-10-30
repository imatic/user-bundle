<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\RoleDocument;

use Imatic\Bundle\UserBundle\RoleDocument\RoleDocument as D;
use Imatic\Bundle\UserBundle\Security\Role\Provider\RoleProviderInterface;
use Imatic\Bundle\UserBundle\Security\Role\Translation\RoleTranslator;
use PHPExcel;
use PHPExcel_Cell_DataType;
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;
use PHPExcel_Worksheet;
use SplFileInfo;
use Symfony\Component\Security\Core\Role\Role;

class RoleDocumentWriter
{
    /**
     * @param array|bool            $defaultRoles   array of default roles, true for all or false for none
     */
    public function __construct(
        private RoleProviderInterface $roleProvider,
        private RoleTranslator $roleTranslator,
        private array|bool $defaultRoles = false
    ) {
        // set default roles
        if (\is_array($defaultRoles)) {
            $defaultRoles = \array_flip($defaultRoles);
        } elseif (!\is_bool($defaultRoles)) {
            throw new \InvalidArgumentException(\sprintf(
                'Invalid default roles. Expected array or boolean, got %s',
                \gettype($defaultRoles)
            ));
        }
        $this->defaultRoles = $defaultRoles;
    }

    /**
     * Create and save the document.
     * @return string file path
     */
    public function write(string $savePath): string
    {
        $document = $this->create();

        $savePathInfo = new SplFileInfo($savePath);
        if (!$savePathInfo->getExtension()) {
            $filePath = \rtrim($savePath, '\\/') . '/role_document.xlsx';
        } else {
            $filePath = $savePath;
        }

        $writer = PHPExcel_IOFactory::createWriter($document, 'Excel2007');
        $writer->save($filePath);

        return $filePath;
    }

    private function create(): PHPExcel
    {
        $document = new PHPExcel();
        $sheet = $document->getActiveSheet();
        $sheet->getStyle('C1')->getFont()->setSize(20);

        $roleMap = $this->getRoleMap();
        foreach ($roleMap as $type => $domains) {
            $row = 1;
            $maxRoles = $this->getMaxRoles($domains);

            // prepare sheet
            if (null === $sheet) {
                $sheet = $document->createSheet();
            }
            $this->prepareSheet($sheet, $type);

            // write
            foreach ($domains as $domain => $labels) {
                $this->writeDomain($sheet, $row++, $domain, $maxRoles);
                foreach ($labels as $roles) {
                    $this->writeRoles($sheet, $row++, $roles);
                }
            }

            $sheet = null;
        }

        return $document;
    }

    private function prepareSheet(PHPExcel_Worksheet $sheet, string $type): void
    {
        $sheet->setTitle($type);
        $sheet->getColumnDimension(D::getColumn(D::COLUMN_INSTRUCTION))->setVisible(false);
        $sheet->getColumnDimension(D::getColumn(D::COLUMN_DATA))->setVisible(false);
        $sheet->getColumnDimension(D::getColumn(D::COLUMN_ROLE_LABEL))->setAutoSize(true);
    }

    private function writeDomain(PHPExcel_Worksheet $sheet, int $row, string $domain, int $maxRoles): void
    {
        $this->writeInstruction($sheet, $row, D::INSTRUCTION_IGNORE);

        $cellCoord = D::getCellCoord($row, D::COLUMN_CONTENT);

        $sheet->mergeCells(
            D::getColumnRange(
                $row,
                D::COLUMN_CONTENT,
                D::COLUMN_CONTENT + $maxRoles * 2 + 1
            )
        );

        $sheet->setCellValue(
            $cellCoord,
            $this->roleTranslator->translateRoleDomain($domain)
        );

        $sheet->getStyle($cellCoord)->applyFromArray([
            'font' => [
                'size' => 20,
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => '004593'],
            ],
        ]);
    }

    /**
     * @param Role[]    $roles
     */
    private function writeRoles(PHPExcel_Worksheet $sheet, int $row, array $roles): void
    {
        if (empty($roles)) {
            $this->writeInstruction($sheet, $row, D::INSTRUCTION_IGNORE);

            return;
        }

        // iterate roles
        $roleNames = [];
        $roleColumn = D::COLUMN_ROLE_STATES_START;
        foreach ($roles as $role) {
            $roleNames[] = $roleName = $role->getRole();
            $roleState = \is_bool($this->defaultRoles)
                ? $this->defaultRoles
                : isset($this->defaultRoles[$roleName]);

            // write action and state
            D::getCell($sheet, $row, $roleColumn)
                ->setValue($this->roleTranslator->translateRoleAction($role->getAction()));
            D::getCell($sheet, $row, $roleColumn + 1)
                ->setValue(D::stateToString($roleState));

            $roleColumn += 2;
        }

        // write instruction
        $this->writeInstruction(
            $sheet,
            $row,
            D::INSTRUCTION_ROLES,
            \implode(';', $roleNames)
        );

        // write label
        D::getCell($sheet, $row, D::COLUMN_ROLE_LABEL)
            ->setValue($this->roleTranslator->translateRole(\current($roles)));
    }

    private function writeInstruction(PHPExcel_Worksheet $sheet, int $row, int $instruction, mixed $data = null): void
    {
        D::getCell($sheet, $row, D::COLUMN_INSTRUCTION)
            ->setValue($instruction);

        if (null !== $data) {
            D::getCell($sheet, $row, D::COLUMN_DATA)
                ->setValueExplicit(
                    (string) $data,
                    PHPExcel_Cell_DataType::TYPE_STRING
                );
        }
    }

    private function getRoleMap(): array
    {
        $roleMap = [];
        foreach ($this->roleProvider->getRoles() as $role) {
            $roleMap[$role->getType()][$role->getDomain()][$role->getLabel()][] = $role;
        }

        return $roleMap;
    }

    /**
     * Get maximum number of roles for given domain.
     */
    private function getMaxRoles(array $domains): int
    {
        $maxRoles = null;
        foreach ($domains as $labels) {
            foreach ($labels as $roles) {
                if (null === $maxRoles) {
                    $maxRoles = \count($roles);
                } else {
                    $maxRoles = \max($maxRoles, \count($roles));
                }
            }
        }

        return $maxRoles;
    }
}
