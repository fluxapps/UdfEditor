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
        return 'UDF Editor';
    }

    protected function uninstallCustom() {
        // TODO: Implement uninstallCustom() method.
    }


}