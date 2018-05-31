<?php

/**
 * Class xudfContentFormGUI
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class xudfContentFormGUI extends ilPropertyFormGUI {

    /**
     * @var ilCtrl
     */
    protected $ctrl;
    /**
     * @var ilLanguage
     */
    protected $lng;
    /**
     * @var ilUdfEditorPlugin
     */
    protected $pl;
    /**
     * @var xudfContentGUI
     */
    protected $parent_gui;
    /**
     * @var int
     */
    protected $obj_id;

    /**
     * xudfSettingsFormGUI constructor.
     * @param xudfContentGUI $parent_gui
     */
    public function __construct(xudfContentGUI $parent_gui) {
        global $DIC;
        $this->ctrl = $DIC['ilCtrl'];
        $this->lng = $DIC['lng'];
        $this->pl = ilUdfEditorPlugin::getInstance();
        $this->parent_gui = $parent_gui;
        $this->obj_id = $parent_gui->getObjId();

//        $this->setTitle($this->lng->txt('settings'));
        $this->setFormAction($this->ctrl->getFormAction($parent_gui));
        $this->initForm();
    }

    /**
     *
     */
    protected function initForm() {
        /** @var xudfContentElement $element */
        foreach (xudfContentElement::where(array('obj_id' => $this->obj_id))->orderBy('sort')->get() as $element) {
            if ($element->isSeparator()) {
                $input = new ilFormSectionHeaderGUI();
                $input->setTitle($element->getTitle());
                $input->setInfo($element->getDescription());
                $this->addItem($input);
            } else {
                switch ($element->getUdfFieldDefinition()['field_type']) {
                    case 1:
                        $input = new ilTextInputGUI($element->getTitle(), $element->getUdfFieldId());
                        break;
                    case 2:
                        $input = new ilSelectInputGUI($element->getTitle(), $element->getUdfFieldId());
                        break;
                    case 3:
                        $input = new ilTextAreaInputGUI($element->getTitle(), $element->getUdfFieldId());
                        break;
                }
                $input->setInfo($element->getDescription());
                $this->addItem($input);
            }
        }

        $this->addCommandButton(xudfSettingsGUI::CMD_UPDATE, $this->lng->txt('save'));
    }

    /**
     *
     */
    public function fillForm() {
        $values = array(
            self::F_TITLE => $this->parent_gui->getObject()->getTitle(),
            self::F_DESCRIPTION => $this->parent_gui->getObject()->getDescription(),
            self::F_ONLINE => $this->settings->isOnline(),
            self::F_SHOW_INFOTAB => $this->settings->isShowInfoTab(),
        );
        $this->setValuesByArray($values);
    }


    /**
     * @return bool
     */
    public function saveForm() {
        if (!$this->checkInput()) {
            return false;
        }

        $this->parent_gui->getObject()->setTitle($this->getInput(self::F_TITLE));
        $this->parent_gui->getObject()->setDescription($this->getInput(self::F_DESCRIPTION));
        $this->parent_gui->getObject()->update();

        $this->settings->setIsOnline($this->getInput(self::F_ONLINE));
        $this->settings->setShowInfoTab($this->getInput(self::F_SHOW_INFOTAB));
        $this->settings->update();

        return true;
    }
}