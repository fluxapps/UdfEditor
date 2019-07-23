<?php

use srag\Plugins\UdfEditor\Exception\UDFNotFoundException;

/**
 * Class xudfFormConfigurationFormGUI
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class xudfFormConfigurationFormGUI extends ilPropertyFormGUI {

    const F_TITLE = 'title';
    const F_DESCRIPTION = 'description';
    const F_UDF_FIELD = 'udf_field';
    const F_IS_SEPARATOR = 'is_separator';
    const F_ELEMENT_ID = 'element_id';


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
     * @var xudfFormConfigurationGUI
     */
    protected $parent_gui;
    /**
     * @var xudfContentElement
     */
    protected $element;

    /**
     * xudfFormConfigurationFormGUI constructor.
     * @param xudfFormConfigurationGUI $parent_gui
     * @param xudfContentElement $element
     */
    public function __construct(xudfFormConfigurationGUI $parent_gui, xudfContentElement $element) {
        global $DIC;
        $this->ctrl = $DIC['ilCtrl'];
        $this->lng = $DIC['lng'];
        $this->pl = ilUdfEditorPlugin::getInstance();
        $this->parent_gui = $parent_gui;
        $this->element = $element;
        $this->setTitle($this->element->getId() ? $this->lng->txt('edit') : $this->lng->txt('create'));
        $this->setFormAction($this->ctrl->getFormAction($parent_gui));

        $this->initForm();

    }

    /**
     *
     */
    protected function initForm() {
        $input = new ilHiddenInputGUI(self::F_IS_SEPARATOR);
        $input->setValue($this->element->isSeparator());
        $this->addItem($input);

        if ($this->element->getId()) {
            $input = new ilHiddenInputGUI(self::F_ELEMENT_ID);
            $input->setValue($this->element->getId());
            $this->addItem($input);
        }

        if ($this->element->isSeparator()) {
            $this->initSeparatorForm();
        } else {
            $this->initUdfFieldForm();
        }

        $this->addCommandButton(xudfFormConfigurationGUI::CMD_CREATE, $this->lng->txt('save'));
        $this->addCommandButton(xudfFormConfigurationGUI::CMD_STANDARD, $this->lng->txt('cancel'));
    }

    /**
     *
     */
    protected function initUdfFieldForm() {
        // UDF FIELD
        $input = new ilSelectInputGUI($this->pl->txt(self::F_UDF_FIELD), self::F_UDF_FIELD);

        /** @var ilUserDefinedFields $udf_fields */
        $udf_fields = ilUserDefinedFields::_getInstance()->getDefinitions();
        $options = array();
        foreach ($udf_fields as $udf_field) {
            $options[$udf_field['field_id']] = $udf_field['field_name'];
        }
        $input->setOptions($options);
        $input->setRequired(true);
        $this->addItem($input);

        // DESCRIPTION
        $input = new ilTextInputGUI($this->lng->txt(self::F_DESCRIPTION), self::F_DESCRIPTION);
        $this->addItem($input);
    }

	/**
	 *
	 */
	protected function initSeparatorForm() {
        // TITLE
        $input = new ilTextInputGUI($this->lng->txt(self::F_TITLE), self::F_TITLE);
        $this->addItem($input);

        // DESCRIPTION
        $input = new ilTextInputGUI($this->lng->txt(self::F_DESCRIPTION), self::F_DESCRIPTION);
        $this->addItem($input);
    }

    /**
     *
     */
    public function fillForm() {
    	try {
			$title = $this->element->getTitle();
		} catch (UDFNotFoundException $e) {
    		ilUtil::sendInfo($this->pl->txt('msg_choose_new_type'));
    		$title = '';
		}
        $values = array(
            self::F_TITLE => $title,
            self::F_DESCRIPTION => $this->element->getDescription(),
            self::F_UDF_FIELD => $this->element->getUdfFieldId(),
        );

        $this->setValuesByArray($values, true);
    }


    /**
     * @return bool
     */
    public function saveForm() {
        if (!$this->checkInput()) {
            return false;
        }

        $this->element->setObjId($this->parent_gui->getObjId());
        $this->element->setTitle($this->getInput(self::F_TITLE));
        $this->element->setDescription($this->getInput(self::F_DESCRIPTION));
        $this->element->setUdfFieldId($this->getInput(self::F_UDF_FIELD));
        $this->element->store();

        return true;
    }
}