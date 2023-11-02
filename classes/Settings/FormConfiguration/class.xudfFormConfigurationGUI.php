<?php

use srag\Notifications4Plugin\UdfEditor\Notification\NotificationCtrl;
use srag\Notifications4Plugin\UdfEditor\Notification\NotificationsCtrl;

/**
 * Class xudfFormConfigurationGUI
 *
 * @author            Theodor Truffer <tt@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy xudfFormConfigurationGUI: ilObjUdfEditorGUI
 */
class xudfFormConfigurationGUI extends xudfGUI
{

    const SUBTAB_SETTINGS = 'settings';
    const SUBTAB_FORM_CONFIGURATION = 'form_configuration';
    const CMD_FORM_CONFIGURATION = 'index';
    const CMD_ADD_UDF_FIELD = 'addUdfField';
    const CMD_ADD_SEPARATOR = 'addSeparator';
    const CMD_CREATE = 'create';
    const CMD_EDIT = 'edit';
    const CMD_UPDATE = 'update';
    const CMD_DELETE = 'delete';
    const CMD_CONFIRM_DELETE = 'confirmDelete';
    const CMD_REORDER = 'reorder';


    /**
     * @param $cmd
     */
    protected function performCommand($cmd)
    {
        switch ($cmd) {
            case self::CMD_STANDARD:
                $this->initToolbar();
                break;
            default:
                break;
        }
        parent::performCommand($cmd);
    }


    /**
     *
     */
    protected function setSubtabs()
    {
        $this->tabs->addSubTab(self::SUBTAB_SETTINGS, $this->lng->txt(self::SUBTAB_SETTINGS), $this->ctrl->getLinkTargetByClass(xudfSettingsGUI::class));
        $this->tabs->addSubTab(self::SUBTAB_FORM_CONFIGURATION, $this->pl->txt(self::SUBTAB_FORM_CONFIGURATION), $this->ctrl->getLinkTargetByClass(xudfFormConfigurationGUI::class, self::CMD_STANDARD));
        $this->ctrl->setParameterByClass(NotificationCtrl::class, NotificationCtrl::GET_PARAM_NOTIFICATION_ID, $this->getObject()->getSettings()->getNotification()->getId());

        //todo
        /*
        if ($this->getObject()->getSettings()->hasMailNotification()) {
            $this->tabs->addSubTab(xudfSettingsGUI::SUBTAB_MAIL_TEMPLATE, $this->pl->txt("notification"),
                $this->ctrl->getLinkTargetByClass([xudfSettingsGUI::class, NotificationsCtrl::class, NotificationCtrl::class], NotificationCtrl::CMD_EDIT_NOTIFICATION));
        }
        */
        $this->tabs->setSubTabActive(self::SUBTAB_FORM_CONFIGURATION);

    }


    /**
     *
     */
    protected function initToolbar()
    {
        $add_udf_field = ilLinkButton::getInstance();
        $add_udf_field->setCaption($this->pl->txt('add_udf_field'), false);
        $add_udf_field->setUrl($this->ctrl->getLinkTarget($this, self::CMD_ADD_UDF_FIELD));
        $this->toolbar->addButtonInstance($add_udf_field);

        $add_separator = $add_udf_field = ilLinkButton::getInstance();
        $add_separator->setCaption($this->pl->txt('add_separator'), false);
        $add_separator->setUrl($this->ctrl->getLinkTarget($this, self::CMD_ADD_SEPARATOR));
        $this->toolbar->addButtonInstance($add_separator);
    }


    /**
     *
     */
    protected function index()
    {
        $xudfFormConfigurationTableGUI = new xudfFormConfigurationTableGUI($this, self::CMD_STANDARD);
        $this->tpl->setContent($xudfFormConfigurationTableGUI->getHTML());
    }


    /**
     *
     */
    protected function addUdfField()
    {
        $udf_fields = ilUserDefinedFields::_getInstance()->getDefinitions();
        if (!count($udf_fields)) {
            ilUtil::sendFailure($this->pl->txt('msg_no_udfs'), true);
            $this->ctrl->redirect($this, self::CMD_STANDARD);
        }
        $xudfFormConfigurationFormGUI = new xudfFormConfigurationFormGUI($this, new xudfContentElement());
        $this->tpl->setContent($xudfFormConfigurationFormGUI->getHTML());
    }


    /**
     *
     */
    protected function addSeparator()
    {
        $element = new xudfContentElement();
        $element->setIsSeparator(true);
        $xudfFormConfigurationFormGUI = new xudfFormConfigurationFormGUI($this, $element);
        $this->tpl->setContent($xudfFormConfigurationFormGUI->getHTML());
    }


    /**
     *
     */
    protected function create()
    {
        $element = new xudfContentElement($_POST['element_id']);
        $element->setIsSeparator($_POST[xudfFormConfigurationFormGUI::F_IS_SEPARATOR]);

        $xudfFormConfigurationFormGUI = new xudfFormConfigurationFormGUI($this, $element);
        $xudfFormConfigurationFormGUI->setValuesByPost();
        if (!$xudfFormConfigurationFormGUI->saveForm()) {
            $this->tpl->setOnScreenMessage('failure', $this->pl->txt('msg_incomplete'), true);
            $this->tpl->setContent($xudfFormConfigurationFormGUI->getHTML());

            return;
        }
        $this->tpl->setOnScreenMessage('success', $this->pl->txt('form_saved'), true);


        $this->ctrl->redirect($this, self::CMD_STANDARD);
    }


    /**
     *
     */
    protected function update()
    {
        $element = new xudfContentElement($_POST['element_id']);

        $xudfFormConfigurationFormGUI = new xudfFormConfigurationFormGUI($this, $element);
        $xudfFormConfigurationFormGUI->setValuesByPost();
        if (!$xudfFormConfigurationFormGUI->saveForm()) {
            $this->tpl->setOnScreenMessage('failure', $this->pl->txt('msg_incomplete'), true);
            $this->tpl->setContent($xudfFormConfigurationFormGUI->getHTML());

            return;
        }
        $this->tpl->setOnScreenMessage('success', $this->pl->txt('form_saved'), true);

        $this->ctrl->redirect($this, self::CMD_STANDARD);
    }


    /**
     *
     */
    protected function edit()
    {
        $element = xudfContentElement::find($_GET['element_id']);
        $xudfFormConfigurationFormGUI = new xudfFormConfigurationFormGUI($this, $element);
        $xudfFormConfigurationFormGUI->fillForm();
        $this->tpl->setContent($xudfFormConfigurationFormGUI->getHTML());
    }


    /**
     *
     */
    protected function delete()
    {
        $element = new xudfContentElement($_GET['element_id']);

        $text = $this->lng->txt('title') . ": {$element->getTitle()}<br>";
        $text .= $this->lng->txt('description') . ": {$element->getDescription()}<br>";
        $text .= $this->lng->txt('type') . ": " . ($element->isSeparator() ? 'Separator' : $this->pl->txt('udf_field'));

        $confirmationGUI = new ilConfirmationGUI();
        $confirmationGUI->addItem('element_id', $_GET['element_id'], $text);
        $confirmationGUI->setFormAction($this->ctrl->getFormAction($this));
        $confirmationGUI->setHeaderText($this->pl->txt('delete_confirmation_text'));
        $confirmationGUI->setConfirm($this->lng->txt('delete'), self::CMD_CONFIRM_DELETE);
        $confirmationGUI->setCancel($this->lng->txt('cancel'), self::CMD_STANDARD);

        $this->tpl->setContent($confirmationGUI->getHTML());
    }


    /**
     *
     */
    protected function confirmDelete()
    {
        $element = new xudfContentElement($_POST['element_id']);
        $element->delete();
        $this->tpl->setOnScreenMessage('success', $this->pl->txt('msg_successfully_deleted'), true);
        $this->ctrl->redirect($this, self::CMD_STANDARD);
    }


    /**
     *
     */
    protected function reorder()
    {
        $sort = 10;
        foreach ($_POST['ids'] as $id) {
            $element = xudfContentElement::find($id);
            $element->setSort($sort);
            $element->update();
            $sort += 10;
        }
    }
}