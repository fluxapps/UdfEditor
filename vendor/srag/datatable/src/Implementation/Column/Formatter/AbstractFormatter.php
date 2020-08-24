<?php

namespace srag\DataTableUI\UdfEditor\Implementation\Column\Formatter;

use srag\DataTableUI\UdfEditor\Component\Column\Formatter\Formatter;
use srag\DataTableUI\UdfEditor\Implementation\Utils\DataTableUITrait;
use srag\DIC\UdfEditor\DICTrait;

/**
 * Class AbstractFormatter
 *
 * @package srag\DataTableUI\UdfEditor\Implementation\Column\Formatter
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractFormatter implements Formatter
{

    use DICTrait;
    use DataTableUITrait;

    /**
     * AbstractFormatter constructor
     */
    public function __construct()
    {

    }
}
