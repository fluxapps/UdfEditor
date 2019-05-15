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
    	$this->checkAndAddCOPageDefinition();

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


	/**
	 * for some reason the entry in copg_pobj_def gets deleted from time to time, so we check and add it everytime now
	 */
    protected function checkAndAddCOPageDefinition() {
	    global $DIC;
    	$sql_query = $DIC->database()->query('SELECT * FROM copg_pobj_def WHERE parent_type = "xudf"');
	    if ($DIC->database()->numRows($sql_query) === 0) {
		    $DIC->database()->insert('copg_pobj_def', array(
			    'parent_type' => array('text', 'xudf'),
			    'class_name' => array('text', 'xudfPageObject'),
			    'directory' => array('text', 'classes/Content/PageEditor'),
			    'component' => array('text', 'Customizing/global/plugins/Services/Repository/RepositoryObject/UdfEditor')
		    ));
	    }
    }

}