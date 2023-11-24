<?php

abstract class xudfGUI
{

    const CMD_STANDARD = 'index';
    protected ilCtrl $ctrl;
    protected ilObjUser $user;
    protected ilLanguage $lng;
    protected ilTemplate|ilGlobalTemplateInterface $tpl;
    protected ilTabsGUI $tabs;
    protected ilToolbarGUI $toolbar;
    protected ilPlugin|ilUdfEditorPlugin $pl;
    protected ilObjUdfEditorGUI $parent_gui;
    protected ilTree $tree;
    protected \ILIAS\DI\LoggingServices $log;


    /**
     * xudfGUI constructor.
     *
     * @param ilObjUdfEditorGUI $parent_gui
     */
    public function __construct(ilObjUdfEditorGUI $parent_gui)
    {
        global $DIC;
        $this->ctrl = $DIC->ctrl();
        $this->user = $DIC->user();
        $this->lng = $DIC->language();
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->tabs = $DIC->tabs();
        $this->toolbar = $DIC->toolbar();
        $this->tree = $DIC->repositoryTree();
        $this->log = $DIC->logger();

        /** @var $component_factory ilComponentFactory */
        $component_factory = $DIC['component.factory'];
        /** @var $plugin ilUdfEditorPlugin */
        $this->pl  = $component_factory->getPlugin(ilUdfEditorPlugin::PLUGIN_ID);

        $this->parent_gui = $parent_gui;
    }

    public function executeCommand(): void
    {
        $this->setSubtabs();
        $next_class = $this->ctrl->getNextClass();
        switch ($next_class) {
            default:
                $cmd = $this->ctrl->getCmd(self::CMD_STANDARD);
                $this->performCommand($cmd);
                break;
        }
    }

    protected function performCommand($cmd): void
    {
        $this->{$cmd}();
    }

    protected function setSubtabs()
    {
        // overwrite if class has subtabs
    }

    public function getObjId(): int
    {
        return $this->parent_gui->getObjId();
    }

    public function getObject(): ilObjUdfEditor
    {
        return $this->parent_gui->getObject();
    }

    protected abstract function index();
}