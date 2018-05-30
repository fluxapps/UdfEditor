<?php
require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Class ilUdfEditorPlugin
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class ilUdfEditorPlugin extends ilRepositoryObjectPlugin {

    const PLUGIN_ID = 'xudf';

    function getPluginName() {
        return 'UdfEditor';
    }

    /**
     * @var ilUdfEditorPlugin
     */
    protected static $instance = NULL;


    /**
     * @return ilUdfEditorPlugin
     */
    public static function getInstance() {
        if (self::$instance === NULL) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    protected function uninstallCustom() {
        // TODO: Implement uninstallCustom() method.
    }


}