<?php
require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Class ilObjUdfEditorListGUI
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class ilObjUdfEditorListGUI extends ilObjectPluginListGUI {

    function getGuiClass() {
        return ilObjUdfEditorGUI::class;
    }

    function initCommands() {
        // TODO: Implement initCommands() method.
    }

    function initType() {
        return ilUdfEditorPlugin::PLUGIN_ID;
    }

}