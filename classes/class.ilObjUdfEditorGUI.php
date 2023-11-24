<?php
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
    const PLUGIN_CLASS_NAME = ilUdfEditorPlugin::class;
    const TAB_CONTENT = 'content';
    const TAB_INFO = 'info';
    const TAB_SETTINGS = 'settings';
    const TAB_HISTORY = 'log_history';
    const TAB_PERMISSIONS = 'permissions';
    const CMD_INDEX = 'index';
    const CMD_SETTINGS = 'showSettings';
    private \ILIAS\DI\LoggingServices $log;
    private ilUdfEditorPlugin $pl;


    public function __construct(int $a_ref_id = 0, int $a_id_type = self::REPOSITORY_NODE_ID, int $a_parent_node_id = 0)
    {
        global $DIC;
        $this->log = $DIC->logger();
        $this->pl = ilUdfEditorPlugin::getInstance();

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
        $next_class = $this->ctrl->getNextClass();
        $cmd = $this->ctrl->getCmd();
        if (!ilObjUdfEditorAccess::hasReadAccess() && $next_class != strtolower(ilInfoScreenGUI::class) && $cmd != "infoScreen") {
            $this->tpl->setOnScreenMessage('failure',  $this->lng->txt("msg_no_perm_read"), true);
            $this->ctrl->returnToParent($this);
        }

       /* try {*/
            switch ($next_class) {
                case strtolower(xudfContentGUI::class):
                    if (!$this->ctrl->isAsynch()) {
                        $this->initHeader();
                        $this->setTabs();
                    }
                    $this->tabs->activateTab(self::TAB_CONTENT);
                    $xvmpGUI = new xudfContentGUI($this);
                    $this->ctrl->forwardCommand($xvmpGUI);
                    $this->tpl->printToStdout();
                    break;
                case strtolower(xudfSettingsGUI::class):
                    if (!ilObjUdfEditorAccess::hasWriteAccess()) {
                        $this->tpl->setOnScreenMessage('failure',  $this->lng->txt('access_denied'), true);
                        $this->ctrl->returnToParent($this);
                    }
                    if (!$this->ctrl->isAsynch()) {
                        $this->initHeader();
                        $this->setTabs();
                    }
                    $this->tabs->activateTab(self::TAB_SETTINGS);
                    $xvmpGUI = new xudfSettingsGUI($this);
                    $this->ctrl->forwardCommand($xvmpGUI);
                    $this->tpl->printToStdout();
                    break;
                case strtolower(xudfFormConfigurationGUI::class):
                    if (!ilObjUdfEditorAccess::hasWriteAccess()) {
                        $this->tpl->setOnScreenMessage('failure',  $this->lng->txt('access_denied'), true);
                        $this->ctrl->returnToParent($this);
                    }
                    if (!$this->ctrl->isAsynch()) {
                        $this->initHeader();
                        $this->setTabs();
                    }
                    $this->tabs->activateTab(self::TAB_SETTINGS);
                    $xvmpGUI = new xudfFormConfigurationGUI($this);
                    $this->ctrl->forwardCommand($xvmpGUI);
                    $this->tpl->printToStdout();
                    break;
                case strtolower(xudfLogGUI::class):
                    if (!ilObjUdfEditorAccess::hasWriteAccess()) {
                        $this->tpl->setOnScreenMessage('failure',  $this->lng->txt('access_denied'), true);
                        $this->ctrl->returnToParent($this);
                    }
                    if (!$this->ctrl->isAsynch()) {
                        $this->initHeader();
                        $this->setTabs();
                    }
                    $this->tabs->activateTab(self::TAB_HISTORY);
                    $xvmpGUI = new xudfLogGUI($this);
                    $this->ctrl->forwardCommand($xvmpGUI);
                    $this->tpl->printToStdout();
                    break;
                case strtolower(ilInfoScreenGUI::class):
                    if (!$this->ctrl->isAsynch()) {
                        $this->initHeader();
                        $this->setTabs();
                    }
                    $this->tabs->activateTab(self::TAB_INFO);
                    $this->checkPermission("visible");
                    $this->infoScreen();    // forwards command
                    $this->tpl->printToStdout();
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
    public function getObjId(): int
    {
        return $this->obj_id;
    }
    public function getObject(): ilObject|ilObjUdfEditor|null
    {
        return $this->object;
    }
    public function performCommand(string $cmd): void
    {
        $this->{$cmd}();
    }

    /**
     * @throws ilCtrlException
     */
    protected function index(): void
    {
        $this->ctrl->redirectByClass(xudfContentGUI::class);
    }
    /**
     * @throws ilCtrlException
     */
    protected function showSettings(): void
    {
        $this->ctrl->redirectByClass(xudfSettingsGUI::class);
    }

    /**
     * @throws ilObjectException
     */
    protected function initHeader($render_locator = true): void
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
    protected function setTabs(): void
    {
        global $DIC;
        $lng = $DIC['lng'];

        $this->tabs->addTab(self::TAB_CONTENT, $this->lng->txt(self::TAB_CONTENT), $this->ctrl->getLinkTargetByClass(xudfContentGUI::class, xudfContentGUI::CMD_STANDARD));

        if (xudfSetting::find($this->obj_id)->isShowInfoTab()) {
            $this->tabs->addTab(self::TAB_INFO, $this->lng->txt(self::TAB_INFO . '_short'), $this->ctrl->getLinkTargetByClass(ilInfoScreenGUI::class));
        }

        if (ilObjUdfEditorAccess::hasWriteAccess()) {
            $this->tabs->addTab(self::TAB_SETTINGS, $this->lng->txt(self::TAB_SETTINGS), $this->ctrl->getLinkTargetByClass(xudfSettingsGUI::class, xudfSettingsGUI::CMD_STANDARD));

            $this->tabs->addTab(self::TAB_HISTORY, $this->lng->txt('history'), $this->ctrl->getLinkTargetByClass(xudfLogGUI::class, xudfLogGUI::CMD_STANDARD));
        }

        if ($this->checkPermissionBool("edit_permission")) {
            $this->tabs->addTab("perm_settings", $lng->txt("perm_settings"), $this->ctrl->getLinkTargetByClass(array(
                get_class($this),
                "ilpermissiongui",
            ), "perm"));
        }
    }
    /**
     * @param ilInfoScreenGUI $info
     */
    function addInfoItems(ilInfoScreenGUI $info): void
    {
        $info->addSection($this->pl->txt('info_section_title'));
        $fields_string = '';
        foreach (xudfContentElement::where(array('obj_id' => $this->getObjId(), 'is_separator' => 0))->get() as $element) {
            /** @var $element xudfContentElement */
            try {
                $fields_string .= $element->getTitle() . '<br>';
            } catch (UDFNotFoundException $e) {
                $this->log->root()->alert($e->getMessage());
                $this->log->root()->alert($e->getTraceAsString());
            }
        }
        $info->addProperty($this->pl->txt('info_section_subtitle'), $fields_string ? $fields_string : '-');
    }
    function getAfterCreationCmd(): string
    {
        return self::CMD_SETTINGS;
    }
    function getStandardCmd(): string
    {
        return self::CMD_INDEX;
    }
    function getType(): string
    {
        return ilUdfEditorPlugin::PLUGIN_ID;
    }
    protected function supportsCloning(): bool
    {
        return false;
    }
}