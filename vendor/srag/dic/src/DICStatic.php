<?php

namespace srag\DIC\UdfEditor;

use ilComponentFactory;
use ilLogLevel;
use ilPlugin;
use ilUdfEditorPlugin;
use srag\DIC\UdfEditor\DIC\DICInterface;
use srag\DIC\UdfEditor\DIC\Implementation\ILIAS60DIC;
use srag\DIC\UdfEditor\DIC\Implementation\ILIAS70DIC;
use srag\DIC\UdfEditor\Exception\DICException;
use srag\DIC\UdfEditor\Output\Output;
use srag\DIC\UdfEditor\Output\OutputInterface;
use srag\DIC\UdfEditor\Plugin\Plugin;
use srag\DIC\UdfEditor\Plugin\PluginInterface;
use srag\DIC\UdfEditor\Version\Version;
use srag\DIC\UdfEditor\Version\VersionInterface;

/**
 * Class DICStatic
 *
 * @package srag\DIC\UdfEditor
 */
final class DICStatic implements DICStaticInterface
{

    /**
     * @var DICInterface|null
     */
    private static $dic = null;
    /**
     * @var OutputInterface|null
     */
    private static $output = null;
    /**
     * @var PluginInterface[]
     */
    private static $plugins = [];
    /**
     * @var VersionInterface|null
     */
    private static $version = null;


    /**
     * DICStatic constructor
     */
    private function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public static function dic() : DICInterface
    {
        if (self::$dic === null) {
            switch (true) {
                case (self::version()->isLower(VersionInterface::ILIAS_VERSION_6)):
                    throw new DICException("DIC not supports ILIAS " . self::version()->getILIASVersion() . " anymore!");

                case (self::version()->isLower(VersionInterface::ILIAS_VERSION_7)):
                    global $DIC;
                    self::$dic = new ILIAS60DIC($DIC);
                    break;

                default:
                    global $DIC;
                    self::$dic = new ILIAS70DIC($DIC);
                    break;
            }
        }

        return self::$dic;
    }


    /**
     * @inheritDoc
     */
    public static function output() : OutputInterface
    {
        if (self::$output === null) {
            self::$output = new Output();
        }

        return self::$output;
    }


    /**
     * @inheritDoc
     */
    public static function plugin(string $plugin_class_name) : PluginInterface
    {
        if (!isset(self::$plugins[$plugin_class_name])) {
            if (!class_exists($plugin_class_name)) {
                throw new DICException("Class $plugin_class_name not exists!", DICException::CODE_INVALID_PLUGIN_CLASS);
            }

            if (method_exists($plugin_class_name, "getInstance")) {
                $plugin_object = $plugin_class_name::getInstance();
            } else {
                //TODO
                global $DIC;
                /** @var $component_factory ilComponentFactory */
                $component_factory = $DIC['component.factory'];
                /** @var $plugin ilUdfEditorPlugin */
                $plugin_object = $component_factory->getPlugin(ilUdfEditorPlugin::PLUGIN_ID);

                //$plugin_object = new $plugin_class_name();

                self::dic()->log()->write("DICLog: Please implement $plugin_class_name::getInstance()!", ilLogLevel::DEBUG);
            }

            if (!$plugin_object instanceof ilPlugin) {
                throw new DICException("Class $plugin_class_name not extends ilPlugin!", DICException::CODE_INVALID_PLUGIN_CLASS);
            }

            self::$plugins[$plugin_class_name] = new Plugin($plugin_object);
        }

        return self::$plugins[$plugin_class_name];
    }


    /**
     * @inheritDoc
     */
    public static function version() : VersionInterface
    {
        if (self::$version === null) {
            self::$version = new Version();
        }

        return self::$version;
    }
}
