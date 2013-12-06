<?php

namespace Imatic\Bundle\UserBundle\RoleDocument;

use SplFileInfo;
use PHPExcel;
use PHPExcel_Worksheet;
use PHPExcel_Cell_DataType;
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Imatic\Bundle\UserBundle\Security\Role\Provider\RoleProviderInterface;
use Imatic\Bundle\UserBundle\Security\Role\Translation\RoleTranslator;
use Imatic\Bundle\UserBundle\RoleDocument\RoleDocument as D;

/**
 * Role document writer
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class RoleDocumentWriter
{
    /** @var RoleProviderInterface */
    private $roleProvider;
    /** @var RoleTranslator */
    private $roleTranslator;
    /** @var array|bool */
    private $defaultRoles;

    /**
     * Constructor
     *
     * @param RoleProviderInterface $roleProvider   role provider
     * @param RoleTranslator        $roleTranslator role translator
     * @param array|bool            $defaultRoles   array of default roles, true for all or false for none
     */
    public function __construct(
        RoleProviderInterface $roleProvider,
        RoleTranslator $roleTranslator,
        $defaultRoles = false
    ) {
        $this->roleProvider = $roleProvider;
        $this->roleTranslator = $roleTranslator;
        
        // set default roles
        if (is_array($defaultRoles)) {
            $defaultRoles = array_flip($defaultRoles);
        } elseif (!is_bool($defaultRoles)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid default roles. Expected array or boolean, got %s',
                gettype($defaultRoles)
            ));
        }
        $this->defaultRoles = $defaultRoles;
    }

    /**
     * Create and save the document
     *
     * @param string $savePath
     * @return string file path
     */
    public function write($savePath)
    {
        $document = $this->create();

        $savePathInfo = new SplFileInfo($savePath);
        if (!$savePathInfo->getExtension()) {
            $filePath = rtrim($savePath, '\\/') . '/role_document.xlsx';
        } else {
            $filePath = $savePath;
        }

        $writer = PHPExcel_IOFactory::createWriter($document, 'Excel2007');
        $writer->save($filePath);

        return $filePath;
    }

    /**
     * Create the document
     *
     * @return PHPExcel
     */
    private function create()
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

    /**
     * Prepare sheet
     *
     * @param PHPExcel_Worksheet $sheet
     * @param string             $type
     */
    private function prepareSheet(PHPExcel_Worksheet $sheet, $type)
    {
        $sheet->setTitle($type);
        $sheet->getColumnDimension(D::getColumn(D::COLUMN_INSTRUCTION))->setVisible(false);
        $sheet->getColumnDimension(D::getColumn(D::COLUMN_DATA))->setVisible(false);
        $sheet->getColumnDimension(D::getColumn(D::COLUMN_ROLE_LABEL))->setAutoSize(true);
    }

    /**
     * Write domain
     *
     * @param PHPExcel_Worksheet $sheet
     * @param int                $row
     * @param string             $domain
     * @param int                $maxRoles
     */
    private function writeDomain(PHPExcel_Worksheet $sheet, $row, $domain, $maxRoles)
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

        $sheet->getStyle($cellCoord)->applyFromArray(array(
            'font' => array(
                'size' => 20,
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '004593'),
            ),
        ));
    }

    /**
     * Write roles
     *
     * @param PHPExcel_Worksheet $sheet
     * @param int                $row
     * @param RoleInterface[]    $roles
     */
    private function writeRoles(PHPExcel_Worksheet $sheet, $row, array $roles)
    {
        if (empty($roles)) {
            $this->writeInstruction($sheet, $row, D::INSTRUCTION_IGNORE);

            return;
        }

        // iterate roles
        $roleNames = array();
        $roleColumn = D::COLUMN_ROLE_STATES_START;
        foreach ($roles as $role) {
            $roleNames[] = $roleName = $role->getRole();
            $roleState = is_bool($this->defaultRoles)
                ? $this->defaultRoles
                : isset($this->defaultRoles[$roleName])
            ;

            // write action and state
            D::getCell($sheet, $row, $roleColumn)
                ->setValue($this->roleTranslator->translateRoleAction($role->getAction()))
            ;
            D::getCell($sheet, $row, $roleColumn + 1)
                ->setValue(D::stateToString($roleState))
            ;

            $roleColumn += 2;
        }

        // write instruction
        $this->writeInstruction(
            $sheet,
            $row,
            D::INSTRUCTION_ROLES,
            implode(';', $roleNames)
        );

        // write label
        D::getCell($sheet, $row, D::COLUMN_ROLE_LABEL)
            ->setValue($this->roleTranslator->translateRole(current($roles)))
        ;
    }

    /**
     * Write instruction and data
     *
     * @param PHPExcel_Worksheet $sheet
     * @param int                $row
     * @param int                $instruction
     * @param mixed              $data
     */
    private function writeInstruction(PHPExcel_Worksheet $sheet, $row, $instruction, $data = null)
    {
        D::getCell($sheet, $row, D::COLUMN_INSTRUCTION)
            ->setValue($instruction)
        ;

        if (null !== $data) {
            D::getCell($sheet, $row, D::COLUMN_DATA)
                ->setValueExplicit(
                    (string) $data,
                    PHPExcel_Cell_DataType::TYPE_STRING
                )
            ;
        }
    }

    /**
     * Get role map
     *
     * @return array
     */
    private function getRoleMap()
    {
        $roleMap = array();
        foreach ($this->roleProvider->getRoles() as $role) {
            $roleMap[$role->getType()][$role->getDomain()][$role->getLabel()][] = $role;
        }

        return $roleMap;
    }

    /**
     * Get maximum number of roles for given domain
     *
     * @param array $domains
     * @return int
     */
    private function getMaxRoles(array $domains)
    {
        $maxRoles = null;
        foreach ($domains as $labels) {
            foreach ($labels as $roles) {
                if (null === $maxRoles) {
                    $maxRoles = sizeof($roles);
                } else {
                    $maxRoles = max($maxRoles, sizeof($roles));
                }
            }
        }

        return $maxRoles;
    }
}
