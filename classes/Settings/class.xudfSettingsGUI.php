<?php

/**
 * Class xudfSettingsGUI
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy xudfSettingsGUI: ilObjUdfEditorGUI, ilPropertyFormGUI
 * @ilCtrl_Calls      xudfSettingsGUI: xudfSettingsFormGUI
 */
class xudfSettingsGUI extends xudfGUI {

    const SUBTAB_SETTINGS = 'settings';
    const SUBTAB_FORM_CONFIGURATION = 'form_configuration';
    const SUBTAB_MAIL_TEMPLATE = 'mail_template';

    const CMD_UPDATE = 'update';


    public function executeCommand()
    {
        $this->setSubtabs();
        $next_class = $this->ctrl->getNextClass();
        switch ($next_class) {
            case strtolower(xudfSettingsFormGUI::class):
                $xudfSettingsFormGUI = new xudfSettingsFormGUI($this);
                $this->ctrl->forwardCommand($xudfSettingsFormGUI);
                break;
            default:
                $cmd = $this->ctrl->getCmd(self::CMD_STANDARD);
                $this->performCommand($cmd);
                break;
        }
    }


    protected function setSubtabs() {
        $this->tabs->addSubTab(self::SUBTAB_SETTINGS, $this->lng->txt(self::SUBTAB_SETTINGS), $this->ctrl->getLinkTarget($this, self::CMD_STANDARD));
        $this->tabs->addSubTab(self::SUBTAB_FORM_CONFIGURATION, $this->pl->txt(self::SUBTAB_FORM_CONFIGURATION), $this->ctrl->getLinkTargetByClass(xudfFormConfigurationGUI::class));
        $this->tabs->addSubTab(self::SUBTAB_MAIL_TEMPLATE, $this->pl->txt(self::SUBTAB_MAIL_TEMPLATE), $this->ctrl->getLinkTargetByClass(xudfFormConfigurationGUI::class));
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
            return;
        }
        ilUtil::sendSuccess($this->pl->txt('form_saved'), true);
        $this->ctrl->redirect($this, self::CMD_STANDARD);
    }



}