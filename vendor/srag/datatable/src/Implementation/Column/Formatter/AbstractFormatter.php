<?php

namespace srag\DataTableUI\UdfEditor\Implementation\Column\Formatter;

use srag\DataTableUI\UdfEditor\Component\Column\Formatter\Formatter;
use srag\DataTableUI\UdfEditor\Implementation\Utils\DataTableUITrait;
use srag\DIC\UdfEditor\DICTrait;

/**
 * Class AbstractFormatter
 *
 * @package srag\DataTableUI\UdfEditor\Implementation\Column\Formatter
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
