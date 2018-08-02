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
            $page_obj->setParentId($parent_gui->getObjId());
            $page_obj->create();
        }


        parent::__construct(xudfPageObject::PARENT_TYPE, $parent_gui->getObjId());

        // content style
        include_once("./Services/Style/Content/classes/class.ilObjStyleSheet.php");

        global $DIC;
        $tpl = $DIC["tpl"];
        $tpl->setCurrentBlock("SyntaxStyle");
        $tpl->setVariable("LOCATION_SYNTAX_STYLESHEET",
            ilObjStyleSheet::getSyntaxStylePath());
        $tpl->parseCurrentBlock();

        $tpl->setCurrentBlock("ContentStyle");
        $tpl->setVariable("LOCATION_CONTENT_STYLESHEET",
            ilObjStyleSheet::getContentStylePath($parent_gui->getObject()->getStyleSheetId()));
        $tpl->parseCurrentBlock();
    }

    function executeCommand() {
        return parent::executeCommand();
    }


}