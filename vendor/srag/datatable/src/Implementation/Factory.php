<?php

namespace srag\DataTableUI\UdfEditor\Implementation;

use srag\DataTableUI\UdfEditor\Component\Column\Factory as ColumnFactoryInterface;
use srag\DataTableUI\UdfEditor\Component\Data\Factory as DataFactoryInterface;
use srag\DataTableUI\UdfEditor\Component\Data\Fetcher\DataFetcher;
use srag\DataTableUI\UdfEditor\Component\Factory as FactoryInterface;
use srag\DataTableUI\UdfEditor\Component\Format\Factory as FormatFactoryInterface;
use srag\DataTableUI\UdfEditor\Component\Settings\Factory as SettingsFactoryInterface;
use srag\DataTableUI\UdfEditor\Component\Table as TableInterface;
use srag\DataTableUI\UdfEditor\Implementation\Column\Factory as ColumnFactory;
use srag\DataTableUI\UdfEditor\Implementation\Data\Factory as DataFactory;
use srag\DataTableUI\UdfEditor\Implementation\Format\Factory as FormatFactory;
use srag\DataTableUI\UdfEditor\Implementation\Settings\Factory as SettingsFactory;
use srag\DataTableUI\UdfEditor\Implementation\Utils\DataTableUITrait;
use srag\DIC\UdfEditor\DICTrait;
use srag\DIC\UdfEditor\Plugin\PluginInterface;
use srag\DIC\UdfEditor\Util\LibraryLanguageInstaller;

/**
 * Class Factory
 *
 * @package srag\DataTableUI\UdfEditor\Implementation
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
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
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function column() : ColumnFactoryInterface
    {
        return ColumnFactory::getInstance();
    }


    /**
     * @inheritDoc
     */
    public function data() : DataFactoryInterface
    {
        return DataFactory::getInstance();
    }


    /**
     * @inheritDoc
     */
    public function format() : FormatFactoryInterface
    {
        return FormatFactory::getInstance();
    }


    /**
     * @inheritDoc
     */
    public function settings() : SettingsFactoryInterface
    {
        return SettingsFactory::getInstance();
    }


    /**
     * @inheritDoc
     */
    public function table(string $table_id, string $action_url, string $title, array $columns, DataFetcher $data_fetcher) : TableInterface
    {
        return new Table($table_id, $action_url, $title, $columns, $data_fetcher);
    }


    /**
     * @inheritDoc
     */
    public function installLanguages(PluginInterface $plugin) : void
    {
        LibraryLanguageInstaller::getInstance()->withPlugin($plugin)->withLibraryLanguageDirectory(__DIR__
            . "/../../lang")->updateLanguages();
    }
}
