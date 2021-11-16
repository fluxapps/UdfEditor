<?php

namespace srag\DIC\UdfEditor\DIC;

use ILIAS\DI\Container;
use srag\DIC\UdfEditor\Database\DatabaseDetector;
use srag\DIC\UdfEditor\Database\DatabaseInterface;

/**
 * Class AbstractDIC
 *
 * @package srag\DIC\UdfEditor\DIC
 */
abstract class AbstractDIC implements DICInterface
{

    /**
     * @var Container
     */
    protected $dic;


    /**
     * @inheritDoc
     */
    public function __construct(Container &$dic)
    {
        $this->dic = &$dic;
    }


    /**
     * @inheritDoc
     */
    public function database() : DatabaseInterface
    {
        return DatabaseDetector::getInstance($this->databaseCore());
    }
}
