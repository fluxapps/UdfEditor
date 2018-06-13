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
     * @var ilObjUser
     */
    protected $user;

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
     * xudfContentFormGUI constructor.
     * @param xudfContentGUI $parent_gui
     * @param boolean $editable
     */
    public function __construct(xudfContentGUI $parent_gui, $editable = true) {
        global $DIC;
        $this->ctrl = $DIC['ilCtrl'];
        $this->lng = $DIC['lng'];
        $this->user = $DIC['ilUser'];
        $this->pl = ilUdfEditorPlugin::getInstance();
        $this->parent_gui = $parent_gui;
        $this->obj_id = $parent_gui->getObjId();

//        $this->setTitle($this->lng->txt('settings'));
        $this->setFormAction($this->ctrl->getFormAction($parent_gui));
        $this->initForm($editable);
    }

    /**
     *
     */
    protected function initForm($editable) {
        /** @var xudfContentElement $element */
        foreach (xudfContentElement::where(array('obj_id' => $this->obj_id))->orderBy('sort')->get() as $element) {
            if ($element->isSeparator()) {
                $input = new ilFormSectionHeaderGUI();
                $input->setTitle($element->getTitle());
                $input->setInfo($element->getDescription());
                $this->addItem($input);
            } else {
                $definition = $element->getUdfFieldDefinition();
                switch ($definition['field_type']) {
                    case 1:
                        $input = new ilTextInputGUI($element->getTitle(), $element->getUdfFieldId());
                        break;
                    case 2:
                        $input = new ilSelectInputGUI($element->getTitle(), $element->getUdfFieldId());
                        $options = array('' => $this->lng->txt('please_choose'));
                        foreach ($definition['field_values'] as $key => $values) {
                            $options[$values] = $values;
                        }
                        $input->setOptions($options);
                        break;
                    case 3:
                        $input = new ilTextAreaInputGUI($element->getTitle(), $element->getUdfFieldId());
                        break;
                }
                $input->setInfo($element->getDescription());
                $input->setRequired(true);
                $input->setDisabled(!$editable);
                $this->addItem($input);
            }
        }

        if ($editable) {
            $this->addCommandButton(xudfSettingsGUI::CMD_UPDATE, $this->lng->txt('save'));
        }
    }

    /**
     *
     */
    public function fillForm() {
        $udf_data = $this->user->getUserDefinedData();
        $values = array();
        /** @var xudfContentElement $element */
        foreach (xudfContentElement::where(array('obj_id' => $this->obj_id, 'is_separator' => false))->get() as $element) {
            $values[$element->getUdfFieldId()] = $udf_data['f_' . $element->getUdfFieldId()];
        }
        $this->setValuesByArray($values);
    }


    /**
     * @return bool
     */
    public function saveForm() {
        if (!$this->checkInput()) {
            return false;
        }

        $udf_data = $this->user->getUserDefinedData();
        /** @var xudfContentElement $element */
        foreach (xudfContentElement::where(array('obj_id' => $this->obj_id, 'is_separator' => false))->get() as $element) {
            $udf_data[$element->getUdfFieldId()] = $this->getInput($element->getUdfFieldId());
        }
        $this->user->setUserDefinedData($udf_data);
        $this->user->updateUserDefinedFields();

        return true;
    }
}