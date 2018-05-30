<?php

/**
 * Class xudfSettingsGUI
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy xudfSettingsGUI: ilObjUdfEditorGUI
 */
class xudfSettingsGUI extends xudfGUI {

    const SUBTAB_SETTINGS = 'settings';
    const SUBTAB_FORM_CONFIGURATION = 'form_configuration';

    const CMD_UPDATE = 'update';

    protected function setSubtabs() {
        $this->tabs->addSubTab(self::SUBTAB_SETTINGS, $this->lng->txt(self::SUBTAB_SETTINGS), $this->ctrl->getLinkTarget($this, self::CMD_STANDARD));
        $this->tabs->addSubTab(self::SUBTAB_FORM_CONFIGURATION, $this->lng->txt(self::SUBTAB_FORM_CONFIGURATION), $this->ctrl->getLinkTargetByClass(xudfFormConfigurationGUI::class));
        $this->tabs->setSubTabActive(self::SUBTAB_SETTINGS);
    }


    protected function index() {
        $xudfSettingsFormGUI = new xudfSettingsFormGUI($this);
        $xudfSettingsFormGUI->fillForm();
        $this->tpl->setContent($xudfSettingsFormGUI->getHTML());
    }

    /**
     *
     */
    protected function update() {
        $xudfSettingsFormGUI = new xudfSettingsFormGUI($this);
        $xudfSettingsFormGUI->setValuesByPost();
        if (!$xudfSettingsFormGUI->saveForm()) {
            ilUtil::sendFailure($this->pl->txt('msg_incomplete'));
            $this->tpl->setContent($xudfSettingsFormGUI->getHTML());
        }
        ilUtil::sendSuccess($this->pl->txt('form_saved'), true);
        $this->ctrl->redirect($this, self::CMD_STANDARD);
    }



}