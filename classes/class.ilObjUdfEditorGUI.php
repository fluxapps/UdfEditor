<?php

use srag\Plugins\UdfEditor\Exception\UDFNotFoundException;
use srag\DIC\UdfEditor\DICTrait;

require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Class ilObjUdfEditorGUI
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ilObjUdfEditorGUI: ilRepositoryGUI, ilObjPluginDispatchGUI, ilAdministrationGUI
 * @ilCtrl_Calls      ilObjUdfEditorGUI: ilPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI, ilCommonActionDispatcherGUI, ilEditClipboardGUI
 */
class ilObjUdfEditorGUI extends ilObjectPluginGUI {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilUdfEditorPlugin::class;

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
    }


    /**
     *
     */
    public function executeCommand() {
        $next_class = self::dic()->ctrl()->getNextClass();
        $cmd = self::dic()->ctrl()->getCmd();
        if (!ilObjUdfEditorAccess::hasReadAccess() && $next_class != "ilinfoscreengui" && $cmd != "infoScreen") {
            ilUtil::sendFailure(self::plugin()->translate('access_denied'), true);
            self::dic()->ctrl()->returnToParent($this);
        }
        $this->tpl->getStandardTemplate();

        try {
            switch ($next_class) {
                case 'xudfcontentgui':
                    if (!self::dic()->ctrl()->isAsynch()) {
                        $this->initHeader();
                        $this->setTabs();
                    }
                    self::dic()->tabs()->activateTab(self::TAB_CONTENT);
                    $xvmpGUI = new xudfContentGUI($this);
                    self::dic()->ctrl()->forwardCommand($xvmpGUI);
                    $this->tpl->show();
                    break;
                case 'xudfsettingsgui':
                    if (!ilObjUdfEditorAccess::hasWriteAccess()) {
                        ilUtil::sendFailure(self::plugin()->translate('access_denied'), true);
                        self::dic()->ctrl()->returnToParent($this);
                    }
                    if (!self::dic()->ctrl()->isAsynch()) {
                        $this->initHeader();
                        $this->setTabs();
                    }
                    self::dic()->tabs()->activateTab(self::TAB_SETTINGS);
                    $xvmpGUI = new xudfSettingsGUI($this);
                    self::dic()->ctrl()->forwardCommand($xvmpGUI);
                    $this->tpl->show();
                    break;
                case 'xudfformconfigurationgui':
                    if (!ilObjUdfEditorAccess::hasWriteAccess()) {
                        ilUtil::sendFailure(self::plugin()->translate('access_denied'), true);
                        self::dic()->ctrl()->returnToParent($this);
                    }
                    if (!self::dic()->ctrl()->isAsynch()) {
                        $this->initHeader();
                        $this->setTabs();
                    }
                    self::dic()->tabs()->activateTab(self::TAB_SETTINGS);
                    $xvmpGUI = new xudfFormConfigurationGUI($this);
                    self::dic()->ctrl()->forwardCommand($xvmpGUI);
                    $this->tpl->show();
                    break;

                case "ilinfoscreengui":
                    if (!self::dic()->ctrl()->isAsynch()) {
                        $this->initHeader();
                        $this->setTabs();
                    }
                    self::dic()->tabs()->activateTab(self::TAB_INFO);
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
            if (!$this->creation_mode) {
                $this->tpl->show();
            }
        }

    }
    
    

    /**
     * @return int
     */
    public function getObjId() {
        return $this->obj_id;
    }

    /**
     * @return ilObjUdfEditor
     */
    public function getObject() {
        return $this->object;
    }


    /**
     * @param $cmd
     */
    protected function performCommand($cmd) {
        $this->{$cmd}();
    }

    /**
     *
     */
    protected function index() {
        self::dic()->ctrl()->redirectByClass(xudfContentGUI::class);
    }

    /**
     *
     */
    protected function showSettings() {
        self::dic()->ctrl()->redirectByClass(xudfSettingsGUI::class);
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

    }


    /**
     * @return bool
     */
    protected function setTabs() {
        global $DIC;
        $lng = $DIC['lng'];

        self::dic()->tabs()->addTab(self::TAB_CONTENT, self::dic()->language()->txt(self::TAB_CONTENT), self::dic()->ctrl()->getLinkTargetByClass(xudfContentGUI::class, xudfContentGUI::CMD_STANDARD));

        if (xudfSetting::find($this->obj_id)->isShowInfoTab()) {
            self::dic()->tabs()->addTab(self::TAB_INFO, self::dic()->language()->txt(self::TAB_INFO . '_short'), self::dic()->ctrl()->getLinkTargetByClass(ilInfoScreenGUI::class));
        }

        if (ilObjUdfEditorAccess::hasWriteAccess()) {
            self::dic()->tabs()->addTab(self::TAB_SETTINGS, self::dic()->language()->txt(self::TAB_SETTINGS), self::dic()->ctrl()->getLinkTargetByClass(xudfSettingsGUI::class, xudfSettingsGUI::CMD_STANDARD));
        }

        if ($this->checkPermissionBool("edit_permission")) {
            self::dic()->tabs()->addTab("perm_settings", $lng->txt("perm_settings"), self::dic()->ctrl()->getLinkTargetByClass(array(
                get_class($this),
                "ilpermissiongui",
            ), "perm"));
        }

        return true;
    }

	/**
	 * @param ilInfoScreenGUI $info
	 * @throws \srag\DIC\UdfEditor\Exception\DICException
	 */
    function addInfoItems($info) {
        $info->addSection(self::plugin()->translate('info_section_title'));
        $fields_string = '';
        foreach (xudfContentElement::where(array('obj_id' => $this->getObjId(), 'is_separator' => 0))->get() as $element) {
            /** @var $element xudfContentElement */
            try {
				$fields_string .= $element->getTitle() . '<br>';
			} catch (UDFNotFoundException $e) {
				self::dic()->logger()->root()->alert($e->getMessage());
				self::dic()->logger()->root()->alert($e->getTraceAsString());
			}
        }
        $info->addProperty(self::plugin()->translate('info_section_subtitle'), $fields_string ? $fields_string : '-');
    }


    /**
     * @return string
     */
    function getAfterCreationCmd() {
        return self::CMD_SETTINGS;
    }

    /**
     * @return string
     */
    function getStandardCmd() {
        return self::CMD_INDEX;
    }

    /**
     * @return string
     */
    function getType() {
        return ilUdfEditorPlugin::PLUGIN_ID;
    }

    /**
     * @return bool
     */
    protected function supportsCloning() {
        return false;
    }

}