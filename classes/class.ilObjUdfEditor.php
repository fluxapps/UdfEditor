<?php
require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Class ilObjUdfEditor
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class ilObjUdfEditor extends ilObjectPlugin {

    /**
     * @return string
     */
    protected function initType() {
        return ilUdfEditorPlugin::PLUGIN_ID;
    }

}