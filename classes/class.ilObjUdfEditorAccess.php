<?php
require_once __DIR__ . "/../vendor/autoload.php";

class ilObjUdfEditorAccess extends ilObjectPluginAccess
{
    protected static ?ilObjUdfEditorAccess $instance = null;

    public static function getInstance(): ?ilObjUdfEditorAccess
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    protected ilAccessHandler $udfeditor_access_handler;
    protected ilObjUser $usr;

    public function __construct()
    {
        global $DIC;

        $this->udfeditor_access_handler = $DIC->access();
        $this->usr = $DIC->user();
    }

    public function _checkAccess(string $cmd, string $permission, ?int $ref_id, ?int $obj_id, ?int $user_id = null): bool
    {
        if ($ref_id === null) {
            $ref_id = filter_input(INPUT_GET, "ref_id");
        }
        if ($obj_id === null) {
            $obj_id = ilObjUdfEditor::_lookupObjectId($ref_id);
        }
        if ($user_id == null) {
            $user_id = $this->usr->getId();
        }

        switch ($permission) {
            case "visible":
            case "read":
                return (($this->udfeditor_access_handler->checkAccessOfUser($user_id, $permission, "", $ref_id) && !self::_isOffline($obj_id))
                    || $this->udfeditor_access_handler->checkAccessOfUser($user_id, "write", "", $ref_id));

            case "delete":
                return ($this->udfeditor_access_handler->checkAccessOfUser($user_id, "delete", "", $ref_id)
                    || $this->udfeditor_access_handler->checkAccessOfUser($user_id, "write", "", $ref_id));

            case "write":
            case "edit_permission":
            default:
                return $this->udfeditor_access_handler->checkAccessOfUser($user_id, $permission, "", $ref_id);
        }
    }
    protected static function checkAccess(string $a_cmd, string $a_permission, int $a_ref_id = null, int $a_obj_id = null, int $a_user_id = null): bool
    {
        return self::getInstance()->_checkAccess($a_cmd, $a_permission, $a_ref_id, $a_obj_id, $a_user_id);
    }
    /**
     * @throws ilCtrlException
     */
    public static function redirectNonAccess(object|string $class, string $cmd = ""): void
    {
        global $DIC;
        $ctrl = $DIC->ctrl();
        $tpl = $DIC->ui()->mainTemplate();
        $tpl->setOnScreenMessage("failure", $DIC->language()->txt("permission_denied"), true);
        if (is_object($class)) {
            $ctrl->clearParameters($class);
            $ctrl->redirect($class, $cmd);
        } else {
            $ctrl->clearParametersByClass($class);
            $ctrl->redirectByClass($class, $cmd);
        }
    }
    public static function hasVisibleAccess(int $ref_id = null): bool
    {
        return self::checkAccess("visible", "visible", $ref_id);
    }
    public static function hasReadAccess(int $ref_id = null): bool
    {
        if(is_null($ref_id)) {
            return true;
        }
        return self::checkAccess("read", "read", $ref_id);
    }
    public static function hasWriteAccess(int $ref_id = null): bool
    {
        if(is_null($ref_id)) {
            return true;
        }
        return self::checkAccess("write", "write", $ref_id);
    }
    public static function hasDeleteAccess(int $ref_id = null): bool
    {
        return self::checkAccess("delete", "delete", $ref_id);
    }
    public static function hasEditPermissionAccess(int $ref_id = null): bool
    {
        return self::checkAccess("edit_permission", "edit_permission", $ref_id);
    }
}