<?php
require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Class ilObjUdfEditorGUI
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class ilObjUdfEditorGUI extends ilObjectPluginGUI {

    const TAB_CONTENT = 'content';
    const TAB_INFO = 'info';
    const TAB_SETTINGS = 'settings';
    const TAB_PERMISSIONS = 'permissions';

    const CMD_INDEX = 'index';
    const CMD_SETTINGS = 'showSettings';


    protected function showSettings() {
        $this->ctrl->redirectByClass(xudfSettingsGUI::class);
    }


    function getAfterCreationCmd() {
        return self::CMD_SETTINGS;
    }

    function getStandardCmd() {
        return self::CMD_INDEX;
    }

    function getType() {
        return ilUdfEditorPlugin::PLUGIN_ID;
    }


}