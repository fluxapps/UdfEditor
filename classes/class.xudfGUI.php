<?php

/**
 * Class xudfGUI
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
abstract class xudfGUI {

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
     * @var ilUdfEditorPlugin
     */
    protected $pl;
    /**
     * @var
     */
    protected $parent_gui;

    /**
     * xudfGUI constructor.
     */
    public function __construct($parent_gui) {
        global $DIC;
        $this->ctrl = $DIC['ctrl'];
        $this->user = $DIC['ilUser'];
        $this->lng = $DIC['lng'];
        $this->tpl = $DIC['tpl'];
        $this->tabs = $DIC['ilTabs'];
        $this->pl = ilUdfEditorPlugin::getInstance();
        $this->parent_gui = $parent_gui;
    }

    /**
     *
     */
    public function executeCommand() {
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
    protected function performCommand($cmd) {
        $this->setSubtabs();
        $this->{$cmd}();
    }

    /**
     *
     */
    protected function setSubtabs() {
        // overwrite if class has subtabs
    }

    /**
     *
     */
    protected abstract function index();
}