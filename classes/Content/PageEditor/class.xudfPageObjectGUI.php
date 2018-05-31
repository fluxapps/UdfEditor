<?php

/**
 * Class xudfPageObjectGUI
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy xudfPageObjectGUI: xudfContentGUI
 * @ilCtrl_Calls xudfPageObjectGUI: ilPageEditorGUI, ilEditClipboardGUI, ilMediaPoolTargetSelector
 * @ilCtrl_Calls xudfPageObjectGUI: ilPublicUserProfileGUI, ilPageObjectGUI
 */
class xudfPageObjectGUI extends ilPageObjectGUI {


    public function __construct(xudfContentGUI $parent_gui) {
        // we always need a page object - create on demand
        if (! xudfPageObject::_exists(xudfPageObject::PARENT_TYPE, $parent_gui->getObjId())) {
            $page_obj = new xudfPageObject();
            $page_obj->setId($parent_gui->getObjId());
            $page_obj->create();
        }


        parent::__construct(xudfPageObject::PARENT_TYPE, $parent_gui->getObjId());
    }

    function executeCommand() {
        return parent::executeCommand();
    }


}