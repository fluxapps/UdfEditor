<?php

namespace srag\DataTableUI\UdfEditor\Implementation\Settings\Storage;

use srag\DataTableUI\UdfEditor\Component\Settings\Storage\Factory as FactoryInterface;
use srag\DataTableUI\UdfEditor\Component\Settings\Storage\SettingsStorage;
use srag\DataTableUI\UdfEditor\Implementation\Utils\DataTableUITrait;
use srag\DIC\UdfEditor\DICTrait;

/**
 * Class Factory
 *
 * @package srag\DataTableUI\UdfEditor\Implementation\Settings\Storage
 */
class Factory implements FactoryInterface
{

    use DICTrait;
    use DataTableUITrait;

    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @inheritDoc
     */
    public function default() : SettingsStorage
    {
        return new DefaultSettingsStorage();
    }
}
