<?php

namespace srag\DataTableUI\UdfEditor\Component\Utils;

use srag\DataTableUI\UdfEditor\Component\Table;

/**
 * Interface TableBuilder
 *
 * @package srag\DataTableUI\UdfEditor\Component\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
interface TableBuilder
{

    /**
     * @return Table
     */
    public function getTable() : Table;


    /**
     * @return string
     */
    public function render() : string;
}
