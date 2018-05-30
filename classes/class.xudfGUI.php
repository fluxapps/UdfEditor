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
     * @var ilUdfEditorPlugin
     */
    protected $pl;

    /**
     * xudfGUI constructor.
     */
    public function __construct() {
        global $DIC;
        $this->ctrl = $DIC['ctrl'];
        $this->user = $DIC['ilUser'];
        $this->lng = $DIC['lng'];
        $this->tpl = $DIC['tpl'];
        $this->pl = ilUdfEditorPlugin::getInstance();
    }

    public function executeCommand() {
        $next_class = $this->ctrl->getNextClass();
        switch ($next_class) {
            default:
                $cmd = $this->ctrl->getCmd(self::CMD_STANDARD);
                $this->{$cmd};
                break;
        }
    }
}