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
            $this->tabs->addSubTab(self::SUBTAB_EDIT_PAGE, $this->lng->txt(self::SUBTAB_EDIT_PAGE), $this->ctrl->getLinkTargetByClass(xudfPageObjectGUI::class, 'edit'));
            $this->tabs->setSubTabActive(self::SUBTAB_SHOW);
        }
    }

    public function executeCommand() {
        $this->setSubtabs();
        $next_class = $this->ctrl->getNextClass();
        switch ($next_class) {
            case 'xudfpageobjectgui':
                $this->tabs->activateSubTab(self::SUBTAB_EDIT_PAGE);
                $xudfPageObjectGUI = new xudfPageObjectGUI($this);
                $html = $this->ctrl->forwardCommand($xudfPageObjectGUI);
                $this->tpl->setContent($html);
                break;
            default:
                $cmd = $this->ctrl->getCmd(self::CMD_STANDARD);
                $this->performCommand($cmd);
                break;
        }
        // these are automatically rendered by the pageobject gui
        $this->tabs->removeTab('edit');
        $this->tabs->removeTab('history');
        $this->tabs->removeTab('clipboard');
        $this->tabs->removeTab('pg');
    }


    protected function index() {
        $page_obj_gui = new xudfPageObjectGUI($this);
        $form = new xudfContentFormGUI($this);
        $form->fillForm();
        $this->tpl->setContent($page_obj_gui->getHTML() . $form->getHTML());
    }

    protected function update() {
        $form = new xudfContentFormGUI($this);
        $form->setValuesByPost();
        if (!$form->saveForm()) {
            ilUtil::sendFailure($this->pl->txt('msg_incomplete'));
            $this->tpl->setContent($form->getHTML());
        }
        ilUtil::sendSuccess($this->pl->txt('form_saved'), true);
        $this->ctrl->redirect($this, self::CMD_STANDARD);
    }
}