<?php

namespace Imatic\Bundle\UserBundle\RoleDocument;

use PHPExcel_Worksheet;
use PHPExcel_Cell;

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

    /**
     * Role state to string.
     *
     * @param bool $state
     *
     * @return string
     */
    public static function stateToString($state)
    {
        return $state ? 'x' : '-';
    }

    /**
     * Role string to state.
     *
     * @param string $string
     *
     * @return bool
     */
    public static function stringToState($string)
    {
        return 'x' === strtolower(trim($string));
    }

    /**
     * Get column name.
     *
     * @param int $column (0 based)
     *
     * @return string
     */
    public static function getColumn($column)
    {
        return PHPExcel_Cell::stringFromColumnIndex($column);
    }

    /**
     * Get cell at given row and column offset.
     *
     * @param PHPExcel_Worksheet $sheet
     * @param int                $row
     * @param int                $column
     *
     * @return PHPExcel_Cell
     */
    public static function getCell(PHPExcel_Worksheet $sheet, $row, $column = 0)
    {
        return $sheet->getCell(self::getCellCoord($row, $column));
    }

    /**
     * Get cell coordinate at given row and column offset.
     *
     * @param int $row
     * @param int $column
     *
     * @return string
     */
    public static function getCellCoord($row, $column = 0)
    {
        return sprintf('%s%s', self::getColumn($column), $row);
    }

    /**
     * Get column range coordinates.
     *
     * @param int $row
     * @param int $startColumn
     * @param int $endColumn
     *
     * @return string
     */
    public static function getColumnRange($row, $startColumn, $endColumn)
    {
        return sprintf(
            '%s:%s',
            self::getCellCoord($row, $startColumn),
            self::getCellCoord($row, $endColumn)
        );
    }

    /**
     * Get row range coordinates.
     *
     * @param int $column
     * @param int $startRow
     * @param int $endRow
     *
     * @return string
     */
    public static function getRowRange($column, $startRow, $endRow)
    {
        return sprintf(
            '%s:%s',
            self::getCellCoord($startRow, $column),
            self::getCellCoord($endRow, $column)
        );
    }
}
