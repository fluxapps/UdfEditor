<?php

namespace srag\DataTableUI\UdfEditor\Component\Utils;

use srag\DataTableUI\UdfEditor\Component\Table;

/**
 * Interface TableBuilder
 *
 * @package srag\DataTableUI\UdfEditor\Component\Utils
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
