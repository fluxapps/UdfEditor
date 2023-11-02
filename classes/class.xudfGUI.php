<?php

/**
 * Class xudfGUI
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
abstract class xudfGUI
{

    const CMD_STANDARD = 'index';
    /**
     * @var ilCtrl
     */
    protected $ctrl;
    /**
     * @var ilObjUser
     */
    protected $user;
    /**
     * @var ilLanguage
     */
    protected $lng;
    /**
     * @var ilTemplate
     */
    protected $tpl;
    /**
     * @var ilTabsGUI
     */
    protected $tabs;
    /**
     * @var ilToolbarGUI
     */
    protected $toolbar;
    /**
     * @var ilUdfEditorPlugin
     */
    protected $pl;
    /**
     * @var
     */
    protected $parent_gui;


    /**
     * xudfGUI constructor.
     *
     * @param ilObjUdfEditorGUI $parent_gui
     */
    public function __construct(ilObjUdfEditorGUI $parent_gui)
    {
        global $DIC;
        $this->ctrl = $DIC['ilCtrl'];
        $this->user = $DIC['ilUser'];
        $this->lng = $DIC['lng'];
        $this->tpl = $DIC['tpl'];
        $this->tabs = $DIC['ilTabs'];
        $this->toolbar = $DIC['ilToolbar'];
        $this->tree = $DIC->repositoryTree();

        /** @var $component_factory ilComponentFactory */
        $component_factory = $DIC['component.factory'];
        /** @var $plugin ilUdfEditorPlugin */
        $this->pl  = $component_factory->getPlugin(ilUdfEditorPlugin::PLUGIN_ID);

        $this->parent_gui = $parent_gui;
    }


    /**
     *
     */
    public function executeCommand()
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


    /**
     * @param $cmd
     */
    protected function performCommand($cmd)
    {
        $this->{$cmd}();
    }


    /**
     *
     */
    protected function setSubtabs()
    {
        // overwrite if class has subtabs
    }


    /**
     * @return int
     */
    public function getObjId()
    {
        return $this->parent_gui->getObjId();
    }


    /**
     * @return ilObjUdfEditor
     */
    public function getObject()
    {
        return $this->parent_gui->getObject();
    }


    /**
     *
     */
    protected abstract function index();
}