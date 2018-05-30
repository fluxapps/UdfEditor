<?php
require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Class ilObjUdfEditorGUI
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class ilObjUdfEditorGUI extends ilObjectPluginGUI {
    function getAfterCreationCmd() {
        // TODO: Implement getAfterCreationCmd() method.
    }

    function getStandardCmd() {
        // TODO: Implement getStandardCmd() method.
    }

    function getType() {
        return ilUdfEditorPlugin::PLUGIN_ID;
    }


}