<?php

use srag\Plugins\UdfEditor\Exception\UDFNotFoundException;
use srag\DIC\UdfEditor\DICTrait;

/**
 * Class xudfContentGUI
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy xudfContentGUI: ilObjUdfEditorGUI
 */
class xudfContentGUI extends xudfGUI {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilUdfEditorPlugin::class;

    const SUBTAB_SHOW = 'show';
    const SUBTAB_EDIT_PAGE = 'edit_page';

    const CMD_RETURN_TO_PARENT = 'returnToParent';


    /**
     *
     */
    protected function setSubtabs() {
        if (ilObjUdfEditorAccess::hasWriteAccess()) {
            self::dic()->tabs()->addSubTab(self::SUBTAB_SHOW, $this->lng->txt(self::SUBTAB_SHOW), self::dic()->ctrl()->getLinkTarget($this));
            self::dic()->tabs()->addSubTab(self::SUBTAB_EDIT_PAGE, $this->lng->txt(self::SUBTAB_EDIT_PAGE), self::dic()->ctrl()->getLinkTargetByClass(xudfPageObjectGUI::class, 'edit'));
            self::dic()->tabs()->setSubTabActive(self::SUBTAB_SHOW);
        }
    }

    /**
     * @throws ilCtrlException
     */
    public function executeCommand() {
        $this->setSubtabs();
        $next_class = self::dic()->ctrl()->getNextClass();
        switch ($next_class) {
            case 'xudfpageobjectgui':
                if (!ilObjUdfEditorAccess::hasWriteAccess()) {
                    ilUtil::sendFailure(self::plugin()->translate('access_denied'), true);
                    self::dic()->ctrl()->returnToParent($this);
                }
                self::dic()->tabs()->activateSubTab(self::SUBTAB_EDIT_PAGE);
                $xudfPageObjectGUI = new xudfPageObjectGUI($this);
                $html = self::dic()->ctrl()->forwardCommand($xudfPageObjectGUI);
                $this->tpl->setContent($html);
                break;
            default:
                $cmd = self::dic()->ctrl()->getCmd(self::CMD_STANDARD);
                $this->performCommand($cmd);
                break;
        }
        // these are automatically rendered by the pageobject gui
        self::dic()->tabs()->removeTab('edit');
        self::dic()->tabs()->removeTab('history');
        self::dic()->tabs()->removeTab('clipboard');
        self::dic()->tabs()->removeTab('pg');
    }


    /**
     *
     */
    protected function index() {
        $has_open_fields = false;
        $where = xudfContentElement::where(array('obj_id' => $this->getObjId()));
        if (!$_GET['edit'] && $where->count()) {
            $udf_values = self::dic()->user()->getUserDefinedData();
            /** @var xudfContentElement $element */
            foreach ($where->get() as $element) {
                if (!$element->isSeparator() && !$udf_values['f_' . $element->getUdfFieldId()]) {
                    $has_open_fields = true;
                    break;
                }
            }
            if (!$has_open_fields) {
                ilUtil::sendInfo(self::plugin()->translate('msg_fields_filled_out'));
                // return button
                $button = ilLinkButton::getInstance();
                $button->setCaption('back');
                $button->setUrl(self::dic()->ctrl()->getLinkTarget($this, self::CMD_RETURN_TO_PARENT));
                $this->toolbar->addButtonInstance($button);
                // edit button
                $button = ilLinkButton::getInstance();
                $button->setCaption('edit');
                self::dic()->ctrl()->setParameter($this, 'edit', 1);
                $button->setUrl(self::dic()->ctrl()->getLinkTarget($this, self::CMD_STANDARD));
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
            ilUtil::sendFailure(self::plugin()->translate('msg_incomplete'));
            $page_obj_gui = new xudfPageObjectGUI($this);
            $this->tpl->setContent($page_obj_gui->getHTML() . $form->getHTML());
            return;
        }
        $this->checkAndSendNotification();
        ilUtil::sendSuccess(self::plugin()->translate('form_saved'), true);
        self::dic()->ctrl()->redirect($this, self::CMD_STANDARD);
    }

    /**
     *
     */
    protected function checkAndSendNotification() {
    	$xudfSettings = xudfSetting::find($this->getObjId());
        if ($xudfSettings->hasMailNotification()) {
            $mail = new ilMail(ANONYMOUS_USER_ID);

            $type = array('normal');

            $mail->setSaveInSentbox(false);
            $mail->appendInstallationSignature(true);

            $settings = new ilSetting();
            $inst_name = $settings->get('short_inst_name');
            $mail->sendMail(
                self::dic()->user()->getLogin(),
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
    	$user = self::dic()->user();
        $body = "Sehr geehrte/r {$user->getFirstname()} {$user->getLastname()},";
        $body .= '
        
        ';
        $body .= 'Sie haben im Objekt „' . $this->getObject()->getTitle() . '“ die folgenden Angaben ausgewählt:';
        $body .= '
        
        ';

        $udf_data = self::dic()->user()->getUserDefinedData();
        foreach (xudfContentElement::where(array('obj_id' => $this->getObjId(), 'is_separator' => false))->get() as $element) {
            /** @var xudfContentElement $element */
            try {
				$body .= $element->getTitle() . ': ' . $udf_data['f_' . $element->getUdfFieldId()];
				$body .= '
            	';
			} catch (UDFNotFoundException $e) {
				self::dic()->logger()->root()->alert($e->getMessage());
				self::dic()->logger()->root()->alert($e->getTraceAsString());
				continue;
			}
        }
        
        return $body;
    }

    /**
     *
     */
    protected function returnToParent() {
        self::dic()->ctrl()->setParameterByClass(ilRepositoryGUI::class, 'ref_id', $this->tree->getParentId($_GET['ref_id']));
        self::dic()->ctrl()->redirectByClass(ilRepositoryGUI::class);
    }
}