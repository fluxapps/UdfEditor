<?php

namespace srag\DataTableUI\UdfEditor\Implementation\Utils;

use srag\DataTableUI\UdfEditor\Component\Factory as FactoryInterface;
use srag\DataTableUI\UdfEditor\Implementation\Factory;

/**
 * Trait DataTableUITrait
 *
 * @package srag\DataTableUI\UdfEditor\Implementation\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait DataTableUITrait
{

    /**
     * @return FactoryInterface
     */
    protected static function dataTableUI() : FactoryInterface
    {
        return Factory::getInstance();
    }
}
