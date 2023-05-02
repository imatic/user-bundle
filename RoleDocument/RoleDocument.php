<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\RoleDocument;

use PHPExcel_Cell;
use PHPExcel_Worksheet;

/**
 * Role document.
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
abstract class RoleDocument
{
    /** Instruction column */
    const COLUMN_INSTRUCTION = 0;
    /** Data column */
    const COLUMN_DATA = 1;
    /** Content column */
    const COLUMN_CONTENT = 2;
    /** Role label column */
    const COLUMN_ROLE_LABEL = 3;
    /** Role states start column */
    const COLUMN_ROLE_STATES_START = 4;

    /** Instruction - ignore row */
    const INSTRUCTION_IGNORE = 0;
    /** Instruction - row containing roles */
    const INSTRUCTION_ROLES = 1;

    /**
     * This is a static class.
     */
    final private function __construct()
    {
    }

    public static function stateToString(bool $state): string
    {
        return $state ? 'x' : '-';
    }

    public static function stringToState(string $string): bool
    {
        return 'x' === \strtolower(\trim($string));
    }

    /**
     * @param int $column (0 based)
     */
    public static function getColumn(int $column): string
    {
        return PHPExcel_Cell::stringFromColumnIndex($column);
    }

    public static function getCell(PHPExcel_Worksheet $sheet, int $row, int $column = 0): PHPExcel_Cell
    {
        return $sheet->getCell(self::getCellCoord($row, $column));
    }

    public static function getCellCoord(int $row, int $column = 0): string
    {
        return \sprintf('%s%s', self::getColumn($column), $row);
    }

    public static function getColumnRange(int $row, int $startColumn, int $endColumn): string
    {
        return \sprintf(
            '%s:%s',
            self::getCellCoord($row, $startColumn),
            self::getCellCoord($row, $endColumn)
        );
    }

    public static function getRowRange(int $column, int $startRow, int $endRow): string
    {
        return \sprintf(
            '%s:%s',
            self::getCellCoord($startRow, $column),
            self::getCellCoord($endRow, $column)
        );
    }
}
