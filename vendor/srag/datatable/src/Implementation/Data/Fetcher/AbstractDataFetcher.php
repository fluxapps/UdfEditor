<?php

namespace srag\DataTableUI\UdfEditor\Implementation\Data\Fetcher;

use srag\DataTableUI\UdfEditor\Component\Data\Fetcher\DataFetcher;
use srag\DataTableUI\UdfEditor\Component\Table;
use srag\DataTableUI\UdfEditor\Implementation\Utils\DataTableUITrait;
use srag\DIC\UdfEditor\DICTrait;

/**
 * Class AbstractDataFetcher
 *
 * @package srag\DataTableUI\UdfEditor\Implementation\Data\Fetcher
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractDataFetcher implements DataFetcher
{

    use DICTrait;
    use DataTableUITrait;

    /**
     * AbstractDataFetcher constructor
     */
    public function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function getNoDataText(Table $component) : string
    {
        return $component->getPlugin()->translate("no_data", Table::LANG_MODULE);
    }


    /**
     * @inheritDoc
     */
    public function isFetchDataNeedsFilterFirstSet() : bool
    {
        return false;
    }
}
