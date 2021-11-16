<?php

namespace srag\DataTableUI\UdfEditor\Component\Data\Fetcher;

use srag\DataTableUI\UdfEditor\Component\Data\Data;
use srag\DataTableUI\UdfEditor\Component\Settings\Settings;
use srag\DataTableUI\UdfEditor\Component\Table;

/**
 * Interface DataFetcher
 *
 * @package srag\DataTableUI\UdfEditor\Component\Data\Fetcher
 */
interface DataFetcher
{

    /**
     * @param Settings $settings
     *
     * @return Data
     */
    public function fetchData(Settings $settings) : Data;


    /**
     * @param Table $component
     *
     * @return string
     */
    public function getNoDataText(Table $component) : string;


    /**
     * @return bool
     */
    public function isFetchDataNeedsFilterFirstSet() : bool;
}
