<?php

use srag\DIC\UdfEditor\DICTrait;
use srag\DIC\UdfEditor\Exception\DICException;
use srag\Plugins\UdfEditor\Exception\UDFNotFoundException;

require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Class ilObjUdfEditorGUI
 *
 * @author            Theodor Truffer <tt@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ilObjUdfEditorGUI: ilRepositoryGUI, ilObjPluginDispatchGUI, ilAdministrationGUI
 * @ilCtrl_Calls      ilObjUdfEditorGUI: ilPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI, ilCommonActionDispatcherGUI, ilEditClipboardGUI
 */
class ilObjUdfEditorGUI extends ilObjectPluginGUI
{

    use DICTrait;
    const PLUGIN_CLASS_NAME = ilUdfEditorPlugin::class;
    const TAB_CONTENT = 'content';
    const TAB_INFO = 'info';
    const TAB_SETTINGS = 'settings';
    const TAB_HISTORY = 'log_history';
    const TAB_PERMISSIONS = 'permissions';
    const CMD_INDEX = 'index';
    const CMD_SETTINGS = 'showSettings';


    public function __construct(int $a_ref_id = 0, int $a_id_type = self::REPOSITORY_NODE_ID, int $a_parent_node_id = 0)
    {
        global $DIC;
        parent::__construct($a_ref_id, $a_id_type, $a_parent_node_id);

        /** @var $component_factory ilComponentFactory */
        $component_factory = $DIC['component.factory'];
        /** @var $plugin ilUdfEditorPlugin */
        $plugin = $component_factory->getPlugin(ilUdfEditorPlugin::PLUGIN_ID);
    }

    /**
     *
     */
    public function executeCommand(): void
    {
        $next_class = self::dic()->ctrl()->getNextClass();
        $cmd = self::dic()->ctrl()->getCmd();
        if (!ilObjUdfEditorAccess::hasReadAccess() && $next_class != strtolower(ilInfoScreenGUI::class) && $cmd != "infoScreen") {
            $this->tpl->setOnScreenMessage('failure',  self::dic()->language()->txt("msg_no_perm_read"), true);
            self::dic()->ctrl()->returnToParent($this);
        }

       /* try {*/
            switch ($next_class) {
                case strtolower(xudfContentGUI::class):
                    if (!self::dic()->ctrl()->isAsynch()) {
                        $this->initHeader();
                        $this->setTabs();
                    }
                    self::dic()->tabs()->activateTab(self::TAB_CONTENT);
                    $xvmpGUI = new xudfContentGUI($this);
                    self::dic()->ctrl()->forwardCommand($xvmpGUI);
                    if (self::version()->is6()) {
                        $this->tpl->printToStdout();
                    } else {
                    $this->tpl->show();
                    }
                    break;
                case strtolower(xudfSettingsGUI::class):
                    if (!ilObjUdfEditorAccess::hasWriteAccess()) {
                        $this->tpl->setOnScreenMessage('failure',  self::dic()->language()->txt('access_denied'), true);
                        self::dic()->ctrl()->returnToParent($this);
                    }
                    if (!self::dic()->ctrl()->isAsynch()) {
                        $this->initHeader();
                        $this->setTabs();
                    }
                    self::dic()->tabs()->activateTab(self::TAB_SETTINGS);
                    $xvmpGUI = new xudfSettingsGUI($this);
                    self::dic()->ctrl()->forwardCommand($xvmpGUI);
                    if (self::version()->is6()) {
                        $this->tpl->printToStdout();
                    } else {
                    $this->tpl->show();
                    }
                    break;
                case strtolower(xudfFormConfigurationGUI::class):
                    if (!ilObjUdfEditorAccess::hasWriteAccess()) {
                        $this->tpl->setOnScreenMessage('failure',  self::dic()->language()->txt('access_denied'), true);
                        self::dic()->ctrl()->returnToParent($this);
                    }
                    if (!self::dic()->ctrl()->isAsynch()) {
                        $this->initHeader();
                        $this->setTabs();
                    }
                    self::dic()->tabs()->activateTab(self::TAB_SETTINGS);
                    $xvmpGUI = new xudfFormConfigurationGUI($this);
                    self::dic()->ctrl()->forwardCommand($xvmpGUI);
                    if (self::version()->is6()) {
                        $this->tpl->printToStdout();
                    } else {
                    $this->tpl->show();
                    }
                    break;
                case strtolower(xudfLogGUI::class):
                    if (!ilObjUdfEditorAccess::hasWriteAccess()) {
                        $this->tpl->setOnScreenMessage('failure',  self::dic()->language()->txt('access_denied'), true);
                        self::dic()->ctrl()->returnToParent($this);
                    }
                    if (!self::dic()->ctrl()->isAsynch()) {
                        $this->initHeader();
                        $this->setTabs();
                    }
                    self::dic()->tabs()->activateTab(self::TAB_HISTORY);
                    $xvmpGUI = new xudfLogGUI($this);
                    self::dic()->ctrl()->forwardCommand($xvmpGUI);
                    if (self::version()->is6()) {
                        $this->tpl->printToStdout();
                    } else {
                    $this->tpl->show();
                    }
                    break;
                case strtolower(ilInfoScreenGUI::class):
                    if (!self::dic()->ctrl()->isAsynch()) {
                        $this->initHeader();
                        $this->setTabs();
                    }
                    self::dic()->tabs()->activateTab(self::TAB_INFO);
                    $this->checkPermission("visible");
                    $this->infoScreen();    // forwards command
                    if (self::version()->is6()) {
                        $this->tpl->printToStdout();
                    } else {
                    $this->tpl->show();
                    }
                    break;
                case strtolower(ilPermissionGUI::class):
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
        /*} catch (Exception $e) {
            $this->tpl->setOnScreenMessage('failure',  $e->getMessage(), true);
            if (!$this->creation_mode) {
                if (self::version()->is6()) {
                    $this->tpl->printToStdout();
                } else {
                $this->tpl->show();
                }
            }
        }*/
    }


    /**
     * @return int
     */
    public function getObjId()
    {
        return $this->obj_id;
    }


    /**
     * @return ilObjUdfEditor
     */
    public function getObject(): ?ilObject
    {
        return $this->object;
    }


    /**
     * @param $cmd
     */
    public function performCommand(string $cmd): void
    {
        $this->{$cmd}();
    }


    /**
     *
     */
    protected function index()
    {
        self::dic()->ctrl()->redirectByClass(xudfContentGUI::class);
    }


    /**
     *
     */
    protected function showSettings()
    {
        self::dic()->ctrl()->redirectByClass(xudfSettingsGUI::class);
    }


    /**
     *
     */
    protected function initHeader($render_locator = true)
    {
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
    protected function setTabs(): void
    {
        global $DIC;
        $lng = $DIC['lng'];

        self::dic()->tabs()->addTab(self::TAB_CONTENT, self::dic()->language()->txt(self::TAB_CONTENT), self::dic()->ctrl()->getLinkTargetByClass(xudfContentGUI::class, xudfContentGUI::CMD_STANDARD));

        if (xudfSetting::find($this->obj_id)->isShowInfoTab()) {
            self::dic()->tabs()->addTab(self::TAB_INFO, self::dic()->language()->txt(self::TAB_INFO . '_short'), self::dic()->ctrl()->getLinkTargetByClass(ilInfoScreenGUI::class));
        }

        if (ilObjUdfEditorAccess::hasWriteAccess()) {
            self::dic()
                ->tabs()
                ->addTab(self::TAB_SETTINGS, self::dic()->language()->txt(self::TAB_SETTINGS), self::dic()->ctrl()->getLinkTargetByClass(xudfSettingsGUI::class, xudfSettingsGUI::CMD_STANDARD));

            self::dic()->tabs()->addTab(self::TAB_HISTORY, self::dic()->language()->txt('history'), self::dic()->ctrl()->getLinkTargetByClass(xudfLogGUI::class, xudfLogGUI::CMD_STANDARD));
        }

        if ($this->checkPermissionBool("edit_permission")) {
            self::dic()->tabs()->addTab("perm_settings", $lng->txt("perm_settings"), self::dic()->ctrl()->getLinkTargetByClass(array(
                get_class($this),
                "ilpermissiongui",
            ), "perm"));
        }
    }


    /**
     * @param ilInfoScreenGUI $info
     *
     * @throws DICException
     */
    function addInfoItems(ilInfoScreenGUI $info): void
    {
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
    function getAfterCreationCmd(): string
    {
        return self::CMD_SETTINGS;
    }


    /**
     * @return string
     */
    function getStandardCmd(): string
    {
        return self::CMD_INDEX;
    }


    /**
     * @return string
     */
    function getType(): string
    {
        return ilUdfEditorPlugin::PLUGIN_ID;
    }


    /**
     * @return bool
     */
    protected function supportsCloning(): bool
    {
        return false;
    }
}