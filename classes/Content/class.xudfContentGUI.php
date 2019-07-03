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

    const CMD_RETURN_TO_PARENT = 'returnToParent';


    /**
     *
     */
    protected function setSubtabs() {
        if (ilObjUdfEditorAccess::hasWriteAccess()) {
            $this->tabs->addSubTab(self::SUBTAB_SHOW, $this->lng->txt(self::SUBTAB_SHOW), $this->ctrl->getLinkTarget($this));
            $this->tabs->addSubTab(self::SUBTAB_EDIT_PAGE, $this->lng->txt(self::SUBTAB_EDIT_PAGE), $this->ctrl->getLinkTargetByClass(xudfPageObjectGUI::class, 'edit'));
            $this->tabs->setSubTabActive(self::SUBTAB_SHOW);
        }
    }

    /**
     * @throws ilCtrlException
     */
    public function executeCommand() {
        $this->setSubtabs();
        $next_class = $this->ctrl->getNextClass();
        switch ($next_class) {
            case 'xudfpageobjectgui':
                if (!ilObjUdfEditorAccess::hasWriteAccess()) {
                    ilUtil::sendFailure($this->pl->txt('access_denied'), true);
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
     */
    protected function index() {
        $has_open_fields = false;
        $where = xudfContentElement::where(array('obj_id' => $this->getObjId()));
        if (!$_GET['edit'] && $where->count()) {
            $udf_values = $this->user->getUserDefinedData();
            /** @var xudfContentElement $element */
            foreach ($where->get() as $element) {
                if (!$element->isSeparator() && !$udf_values['f_' . $element->getUdfFieldId()]) {
                    $has_open_fields = true;
                    break;
                }
            }
            if (!$has_open_fields) {
                ilUtil::sendInfo($this->pl->txt('msg_fields_filled_out'));
                // return button
                $button = ilLinkButton::getInstance();
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
     *
     */
    protected function update() {
        $form = new xudfContentFormGUI($this);
        $form->setValuesByPost();
        if (!$form->saveForm()) {
            ilUtil::sendFailure($this->pl->txt('msg_incomplete'));
            $page_obj_gui = new xudfPageObjectGUI($this);
            $this->tpl->setContent($page_obj_gui->getHTML() . $form->getHTML());
            return;
        }
        $this->checkAndSendNotification();
        ilUtil::sendSuccess($this->pl->txt('form_saved'), true);
        $this->ctrl->redirect($this, self::CMD_STANDARD);
    }

    /**
     *
     */
    protected function checkAndSendNotification() {
    	$xudfSettings = xudfSetting::find($this->getObjId());
        if ($xudfSettings->hasMailNotification()) {
            $mail = new ilMail(ANONYMOUS_USER_ID);

            $type = array('system');

            $mail->setSaveInSentbox(false);
            $mail->appendInstallationSignature(true);

            $settings = new ilSetting();
            $inst_name = $settings->get('short_inst_name');
            $mail->sendMail(
                $this->user->getLogin(),
                '',
				$xudfSettings->getAdditionalNotification(),
                ($inst_name ? $inst_name : 'ILIAS') . ': ' . $this->getObject()->getTitle(),
                $this->getNotificationMailBody(),
                array(),
                $type
            );
        }
    }

    protected function getNotificationMailBody() {
        $body = "Sehr geehrte/r {$this->user->getFirstname()} {$this->user->getLastname()},";
        $body .= '
        
        ';
        $body .= 'Sie haben im Objekt „' . $this->getObject()->getTitle() . '“ die folgenden Angaben ausgewählt:';
        $body .= '
        
        ';

        $udf_data = $this->user->getUserDefinedData();
        foreach (xudfContentElement::where(array('obj_id' => $this->getObjId(), 'is_separator' => false))->get() as $element) {
            /** @var xudfContentElement $element */
            $body .= $element->getTitle() . ': ' . $udf_data['f_' . $element->getUdfFieldId()];
            $body .= '
            ';
        }
        
        return $body;
    }

    /**
     *
     */
    protected function returnToParent() {
        $this->ctrl->setParameterByClass(ilRepositoryGUI::class, 'ref_id', $this->tree->getParentId($_GET['ref_id']));
        $this->ctrl->redirectByClass(ilRepositoryGUI::class);
    }
}