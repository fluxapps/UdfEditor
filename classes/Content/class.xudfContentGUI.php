<?php

use srag\Plugins\UdfEditor\Exception\UDFNotFoundException;

/**
 * @ilCtrl_isCalledBy xudfContentGUI: ilObjUdfEditorGUI
 */
class xudfContentGUI extends xudfGUI {

	const PLUGIN_CLASS_NAME = ilUdfEditorPlugin::class;

    const SUBTAB_SHOW = 'show';
    const SUBTAB_EDIT_PAGE = 'edit_page';

    const CMD_RETURN_TO_PARENT = 'returnToParent';

    protected function setSubtabs(): void
    {
        if (ilObjUdfEditorAccess::hasWriteAccess()) {
            $this->tabs->addSubTab(self::SUBTAB_SHOW, $this->lng->txt(self::SUBTAB_SHOW), $this->ctrl->getLinkTarget($this));
            $this->tabs->addSubTab(self::SUBTAB_EDIT_PAGE, $this->lng->txt(self::SUBTAB_EDIT_PAGE), $this->ctrl->getLinkTargetByClass(xudfPageObjectGUI::class, 'edit'));
            $this->tabs->setSubTabActive(self::SUBTAB_SHOW);
        }
    }

    /**
     * @throws ilCtrlException
     */
    public function executeCommand(): void
    {
        $this->setSubtabs();
        $next_class = $this->ctrl->getNextClass();
        switch ($next_class) {
            case 'xudfpageobjectgui':
                if (!ilObjUdfEditorAccess::hasWriteAccess()) {
                    $this->tpl->setOnScreenMessage("failure", $this->pl->txt('access_denied'), true);
                    $this->ctrl->returnToParent($this);
                }
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


    /**
     *
     * @throws \srag\Plugins\UdfEditor\Exception\UnknownUdfTypeException
     */
    protected function index(): void
    {
        $has_open_fields = false;
        $where = xudfContentElement::where(array('obj_id' => $this->getObjId()));
        if (!$_GET['edit'] && $where->count()) {
            $udf_values = $this->user->getUserDefinedData();
            /** @var xudfContentElement $element */
            foreach ($where->get() as $element) {
                if (!$element->isSeparator() && array_key_exists('f_' . $element->getUdfFieldId(),$udf_values) && !$udf_values['f_' . $element->getUdfFieldId()]) {
                    $has_open_fields = true;
                    break;
                }
            }
            if (!$has_open_fields) {
                // return button
                $button = ilLinkButton::getInstance();
                $button->setPrimary(true);
                $button->setCaption('back');
                $button->setUrl($this->ctrl->getLinkTarget($this, self::CMD_RETURN_TO_PARENT));
                $this->toolbar->addButtonInstance($button);
                // edit button
                $button = ilLinkButton::getInstance();
                $button->setCaption('edit');
                $this->ctrl->setParameter($this, 'edit', 1);
                $button->setUrl($this->ctrl->getLinkTarget($this, self::CMD_STANDARD));
                $this->toolbar->addButtonInstance($button);
            }
        }
        $page_obj_gui = new xudfPageObjectGUI($this);
        $form = new xudfContentFormGUI($this, $has_open_fields || $_GET['edit']);
        $form->fillForm();
        $this->tpl->setContent($page_obj_gui->getHTML() . $form->getHTML());
    }

    /**
     * @throws ilCtrlException
     * @throws \srag\Plugins\UdfEditor\Exception\UnknownUdfTypeException
     */
    protected function update(): void
    {
        $form = new xudfContentFormGUI($this);
        $form->setValuesByPost();
        if (!$form->saveForm()) {
            $this->tpl->setOnScreenMessage('failure', $this->pl->txt('msg_incomplete'), true);
            $page_obj_gui = new xudfPageObjectGUI($this);
            $this->tpl->setContent($page_obj_gui->getHTML() . $form->getHTML());
            return;
        }
        $this->checkAndSendNotification();
        $this->tpl->setOnScreenMessage('success', $this->pl->txt('content_form_saved'), true);
        $this->redirectAfterSave();
        $this->ctrl->redirect($this, self::CMD_STANDARD);
    }

    /**
     *
     */
    protected function checkAndSendNotification() {
        $xudfSettings = $this->getObject()->getSettings();

        /*if ($xudfSettings->hasMailNotification()) {

            $notification = $xudfSettings->getNotification();

            $sender = self::notifications4plugin()->sender()->factory()->internalMail(ANONYMOUS_USER_ID, $this->user->getId());

            $sender->setBcc($xudfSettings->getAdditionalNotification());

            $user_defined_data = [];
            $udf_data = $this->user->getUserDefinedData();
            foreach (xudfContentElement::where(array('obj_id' => $this->getObjId(), 'is_separator' => false))->get() as $element) {/*
                /** @var xudfContentElement $element */
      /*          try {
                    $user_defined_data[$element->getTitle()] = $udf_data['f_' . $element->getUdfFieldId()];
                } catch (UDFNotFoundException $e) {
                    $this->log->root()->alert($e->getMessage());
                    $this->log->root()->alert($e->getTraceAsString());
                    continue;
                }
            }

            $placeholders = [
                "object" => $this->getObject(),
                "user" => $this->user,
                "user_defined_data" => $user_defined_data
            ];

            try {
                self::notifications4plugin()->sender()->send($sender, $notification, $placeholders, $placeholders["user"]->getLanguage());
            } catch (Notifications4PluginException $e) {
                $this->log->root()->alert($e->getMessage());
                $this->log->root()->alert($e->getTraceAsString());
            }
        }*/
    }


    /**
     *
     */
    protected function returnToParent() {
        $this->ctrl->setParameterByClass(ilRepositoryGUI::class, 'ref_id', $this->tree->getParentId($_GET['ref_id']));
        $this->ctrl->redirectByClass(ilRepositoryGUI::class);
    }


    /**
     *
     */
    protected function redirectAfterSave()
    {
       switch ($this->getObject()->getSettings()->getRedirectType())  {
           case xudfSetting::REDIRECT_STAY_IN_FORM:
               $this->ctrl->redirect($this);
               break;
           case xudfSetting::REDIRECT_TO_ILIAS_OBJECT:
               $ref_id = $this->getObject()->getSettings()->getRedirectValue();
               $this->ctrl->setParameterByClass(ilRepositoryGUI::class, 'ref_id', $ref_id);
               $this->ctrl->redirectByClass(ilRepositoryGUI::class);
               break;
           case xudfSetting::REDIRECT_TO_URL:
               $url = $this->getObject()->getSettings()->getRedirectValue();
               $this->ctrl->redirectToURL($url);
               break;
       }
    }
}