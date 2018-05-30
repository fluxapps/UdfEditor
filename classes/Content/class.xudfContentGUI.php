<?php

/**
 * Class xudfContentGUI
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy xudfContentGUI: ilObjUdfEditorGUI
 */
class xudfContentGUI extends xudfGUI {

    const SUBTAB_SHOW = 'show';
    const SUBTAB_EDIT_PAGE = 'edit_page';


    protected function setSubtabs() {
        if (ilObjUdfEditorAccess::hasWriteAccess()) {
            $this->tabs->addSubTab(self::SUBTAB_SHOW, $this->lng->txt(self::SUBTAB_SHOW), $this->ctrl->getLinkTarget($this));
            $this->tabs->addSubTab(self::SUBTAB_EDIT_PAGE, $this->lng->txt(self::SUBTAB_EDIT_PAGE), $this->ctrl->getLinkTargetByClass(xudfFormConfigurationGUI::class, self::CMD_STANDARD));
            $this->tabs->setSubTabActive(self::SUBTAB_SHOW);
        }
    }


    protected function index() {
        echo 123;
    }
}