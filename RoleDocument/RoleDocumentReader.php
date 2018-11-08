<?php
namespace Imatic\Bundle\UserBundle\RoleDocument;

use ArrayIterator;
use Imatic\Bundle\UserBundle\RoleDocument\RoleDocument as D;
use IteratorAggregate;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;

/**
 * Role document reader.
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class RoleDocumentReader implements IteratorAggregate
{
    /** @var PHPExcel|null */
    private $document;

    /**
     * Open document.
     *
     * @param string $path
     *
     * @return RoleDocumentReader
     */
    public function open($path)
    {
        $reader = PHPExcel_IOFactory::createReader('Excel2007');

        if (!\is_file($path)) {
            throw new \InvalidArgumentException('File not found');
        }

        if (!$reader->canRead($path)) {
            throw new \RuntimeException('The document could not be read');
        }

        $this->document = $reader->load($path);
    }

    /**
     * Get role iterator.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->readRoles());
    }

    /**
     * Read enabled roles.
     *
     * @return array
     */
    public function readEnabledRoles()
    {
        return \array_keys(\array_filter($this->readRoles()));
    }

    /**
     * Read roles.
     *
     * @return array
     */
    public function readRoles()
    {
        if (null === $this->document) {
            throw new \RuntimeException('No document opened');
        }

        $roles = [];
        $sheetCount = $this->document->getSheetCount();
        for ($sheetIndex = 0; $sheetIndex < $sheetCount; ++$sheetIndex) {
            $sheet = $this->document->getSheet($sheetIndex);
            $row = 1;
            $atEnd = false;

            do {
                $instruction = D::getCell($sheet, $row, D::COLUMN_INSTRUCTION)->getValue();
                if (null === $instruction) {
                    $atEnd = true;
                } else {
                    switch ($instruction) {
                        case D::INSTRUCTION_IGNORE:
                            break;
                        case D::INSTRUCTION_ROLES:
                            $roles += $this->readRoleRow($sheet, $row);
                            break;
                        default:
                            throw new \OutOfBoundsException(\sprintf('Invalid instruction "%s"', $instruction));
                    }
                }

                ++$row;
            } while (!$atEnd);
        }

        return $roles;
    }

    /**
     * Read role row.
     *
     * @param PHPExcel_Worksheet $sheet
     * @param int                $row
     *
     * @return array
     */
    private function readRoleRow(PHPExcel_Worksheet $sheet, $row)
    {
        $roles = \explode(';', D::getCell($sheet, $row, D::COLUMN_DATA)->getValue());
        $roleCount = \count($roles);

        $column = D::COLUMN_ROLE_STATES_START;
        $states = [];
        for ($i = 0; $i < $roleCount; ++$i) {
            $states[] = D::stringToState(
                D::getCell($sheet, $row, $column + 1)->getValue()
            );

            $column += 2;
        }

        return \array_combine($roles, $states);
    }
}
