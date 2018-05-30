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

    /**
     *
     */
    protected function doCreate() {
        $xudfSetting = new xudfSetting();
        $xudfSetting->setObjId($this->getId());
        $xudfSetting->create();
    }

    /**
     *
     */
    protected function beforeDelete() {
        xudfSetting::find($this->getId())->delete();
    }


}