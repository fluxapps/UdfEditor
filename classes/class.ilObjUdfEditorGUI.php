<?php
require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Class ilObjUdfEditorGUI
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ilObjViMPGUI: ilRepositoryGUI, ilObjPluginDispatchGUI, ilAdministrationGUI
 * @ilCtrl_Calls      ilObjViMPGUI: ilPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI, ilCommonActionDispatcherGUI
 */
class ilObjUdfEditorGUI extends ilObjectPluginGUI {

    const TAB_CONTENT = 'content';
    const TAB_INFO = 'info';
    const TAB_SETTINGS = 'settings';
    const TAB_PERMISSIONS = 'permissions';

    const CMD_INDEX = 'index';
    const CMD_SETTINGS = 'showSettings';

    /**
     * ilObjUdfEditorGUI constructor.
     */
    public function __construct($a_ref_id = 0, $a_id_type = self::REPOSITORY_NODE_ID, $a_parent_node_id = 0) {
        parent::__construct($a_ref_id, $a_id_type, $a_parent_node_id);
        $this->pl = ilUdfEditorPlugin::getInstance();
    }


    /**
     *
     */
    public function executeCommand() {
        $next_class = $this->ctrl->getNextClass();
        $cmd = $this->ctrl->getCmd();
        if (!ilObjViMPAccess::hasReadAccess() && $next_class != "ilinfoscreengui" && $cmd != "infoScreen") {
            ilUtil::sendFailure($this->pl->txt('access_denied'), true);
            $this->ctrl->returnToParent($this);
        }
        $this->tpl->getStandardTemplate();

        try {
            switch ($next_class) {
                case 'xudfcontentgui':
                    if (!$this->ctrl->isAsynch()) {
                        $this->initHeader();
                        $this->setTabs();
                    }
                    $xvmpGUI = new xudfContentGUI($this);
                    $this->ctrl->forwardCommand($xvmpGUI);
                    $this->tpl->show();
                    break;
                case 'xudfsettingsgui':
                    if (!$this->ctrl->isAsynch()) {
                        $this->initHeader();
                        $this->setTabs();
                    }
                    $xvmpGUI = new xudfSettingsGUI($this);
                    $this->ctrl->forwardCommand($xvmpGUI);
                    $this->tpl->show();
                    break;
                case "ilinfoscreengui":
                    if (!$this->ctrl->isAsynch()) {
                        $this->initHeader();
                        $this->setTabs();
                    }
                    $this->checkPermission("visible");
                    $this->infoScreen();	// forwards command
                    $this->tpl->show();
                    break;
                case 'ilpermissiongui':
                    $this->initHeader(false);
                    parent::executeCommand();
                    break;
                default:
                    // workaround for object deletion; 'parent::executeCommand()' shows the template and leads to "Headers already sent" error
                    if ($next_class == "" && $cmd == 'deleteObject') {
                        $this->deleteObject();
                        break;
                    }
                    parent::executeCommand();
                    break;
            }
        } catch (Exception $e) {
            ilUtil::sendFailure($e->getMessage());
            $this->tpl->show();
        }

    }

    protected function showSettings() {
        $this->ctrl->redirectByClass(xudfSettingsGUI::class);
    }

    /**
     *
     */
    protected function initHeader($render_locator = true) {
        if ($render_locator) {
            $this->setLocator();
        }
        $this->tpl->setTitleIcon(ilObjUdfEditor::_getIcon($this->object_id));
        $this->tpl->setTitle($this->object->getTitle());
        $this->tpl->setDescription($this->object->getDescription());

        if (!xudfSetting::find($this->obj_id)->isOnline()) {
            /**
             * @var $list_gui ilObjUdfEditorListGUI
             */
            $list_gui = ilObjectListGUIFactory::_getListGUIByType('xudf');
            $this->tpl->setAlertProperties($list_gui->getAlertProperties());
        }

//		$this->tpl->setTitleIcon(ilObjViMP::_getIcon($this->object_id));
//        $this->tpl->setPermanentLink('xvmp', $_GET['ref_id']);
    }

    /**
     * @return bool
     */
    protected function setTabs() {
        global $DIC;
        $lng = $DIC['lng'];

        $this->tabs_gui->addTab(self::TAB_CONTENT, $this->pl->txt(self::TAB_CONTENT), $this->ctrl->getLinkTargetByClass(xudfContentGUI::class, xudfContentGUI::CMD_STANDARD));

        if (xudfSetting::find($this->obj_id)->isShowInfoTab()) {
            $this->tabs_gui->addTab(self::TAB_INFO, $this->pl->txt(self::TAB_INFO), $this->ctrl->getLinkTargetByClass(ilInfoScreenGUI::class));
        }

        if (ilObjUdfEditorAccess::hasWriteAccess()) {
            $this->tabs_gui->addTab(self::TAB_SETTINGS, $this->pl->txt(self::TAB_SETTINGS), $this->ctrl->getLinkTargetByClass(xudfSettingsGUI::class, xudfSettingsGUI::CMD_STANDARD));
        }

        if ($this->checkPermissionBool("edit_permission")) {
            $this->tabs_gui->addTab("perm_settings", $lng->txt("perm_settings"), $this->ctrl->getLinkTargetByClass(array(
                get_class($this),
                "ilpermissiongui",
            ), "perm"));
        }

        return true;
    }

    function getAfterCreationCmd() {
        return self::CMD_SETTINGS;
    }

    function getStandardCmd() {
        return self::CMD_INDEX;
    }

    function getType() {
        return ilUdfEditorPlugin::PLUGIN_ID;
    }


}