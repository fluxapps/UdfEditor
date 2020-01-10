<?php

namespace srag\Notifications4Plugin\UdfEditor\Parser;

use srag\DIC\UdfEditor\DICTrait;
use srag\Notifications4Plugin\UdfEditor\Utils\Notifications4PluginTrait;

/**
 * Class Factory
 *
 * @package srag\Notifications4Plugin\UdfEditor\Parser
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory implements FactoryInterface
{

    use DICTrait;
    use Notifications4PluginTrait;
    /**
     * @var FactoryInterface|null
     */
    protected static $instance = null;


    /**
     * @return FactoryInterface
     */
    public static function getInstance() : FactoryInterface
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
     * @inheritdoc
     */
    public function twig() : twigParser
    {
        return new twigParser();
    }
}
