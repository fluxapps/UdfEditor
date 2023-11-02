<?php

use srag\Notifications4Plugin\UdfEditor\Exception\Notifications4PluginException;
use srag\Notifications4Plugin\UdfEditor\Utils\Notifications4PluginTrait;
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
	use Notifications4PluginTrait;
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
            $this->tpl->setOnScreenMessage('failure', $this->pl->txt('msg_incomplete'), true);
            $page_obj_gui = new xudfPageObjectGUI($this);
            $this->tpl->setContent($page_obj_gui->getHTML() . $form->getHTML());
            return;
        }
        $this->checkAndSendNotification();
        $this->tpl->setOnScreenMessage('success', $this->pl->txt('content_form_saved'), true);
        $this->redirectAfterSave();
        self::dic()->ctrl()->redirect($this, self::CMD_STANDARD);
    }

    /**
     *
     */
    protected function checkAndSendNotification() {
        $xudfSettings = $this->getObject()->getSettings();

        if ($xudfSettings->hasMailNotification()) {

            $notification = $xudfSettings->getNotification();

            $sender = self::notifications4plugin()->sender()->factory()->internalMail(ANONYMOUS_USER_ID, self::dic()->user()->getId());

            $sender->setBcc($xudfSettings->getAdditionalNotification());

            $user_defined_data = [];
            $udf_data = self::dic()->user()->getUserDefinedData();
            foreach (xudfContentElement::where(array('obj_id' => $this->getObjId(), 'is_separator' => false))->get() as $element) {
                /** @var xudfContentElement $element */
                try {
                    $user_defined_data[$element->getTitle()] = $udf_data['f_' . $element->getUdfFieldId()];
                } catch (UDFNotFoundException $e) {
                    self::dic()->logger()->root()->alert($e->getMessage());
                    self::dic()->logger()->root()->alert($e->getTraceAsString());
                    continue;
                }
            }

            $placeholders = [
                "object" => $this->getObject(),
                "user" => self::dic()->user(),
                "user_defined_data" => $user_defined_data
            ];

            try {
                self::notifications4plugin()->sender()->send($sender, $notification, $placeholders, $placeholders["user"]->getLanguage());
            } catch (Notifications4PluginException $e) {
                self::dic()->logger()->root()->alert($e->getMessage());
                self::dic()->logger()->root()->alert($e->getTraceAsString());
            }
        }
    }


    /**
     *
     */
    protected function returnToParent() {
        self::dic()->ctrl()->setParameterByClass(ilRepositoryGUI::class, 'ref_id', $this->tree->getParentId($_GET['ref_id']));
        self::dic()->ctrl()->redirectByClass(ilRepositoryGUI::class);
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