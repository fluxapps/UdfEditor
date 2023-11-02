<?php
require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Class ilObjUdfEditorAccess
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class ilObjUdfEditorAccess extends ilObjectPluginAccess
{

    /**
     * @var ilObjUdfEditorAccess
     */
    protected static ?ilObjUdfEditorAccess $instance = null;


    /**
     * @return ilObjUdfEditorAccess
     */
    public static function getInstance(): ?ilObjUdfEditorAccess
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @var ilObjUdfEditorAccessHandler
     */
    protected ilAccessHandler|ilObjUdfEditorAccessHandler $udfeditor_access_handler;
    /**
     * @var ilObjUser
     */
    protected $usr;


    /**
     *
     */
    public function __construct()
    {
        global $DIC;

        $this->udfeditor_access_handler = $DIC->access();
        $this->usr = $DIC->user();
    }


    /**
     * @param string   $a_cmd
     * @param string   $a_permission
     * @param int|null $a_ref_id
     * @param int|null $a_obj_id
     * @param int|null $a_user_id
     *
     * @return bool
     */
    public function _checkAccess(string $cmd, string $permission, int $ref_id, int $obj_id, ?int $user_id = null): bool
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


    /**
     * @param string $a_cmd
     * @param string $a_permission
     * @param int|null $a_ref_id
     * @param int|null $a_obj_id
     * @param int|null $a_user_id
     *
     * @return bool
     */
    protected static function checkAccess(string $a_cmd, string $a_permission, int $a_ref_id = null, int $a_obj_id = null, int $a_user_id = null): bool
    {
        return self::getInstance()->_checkAccess($a_cmd, $a_permission, $a_ref_id, $a_obj_id, $a_user_id);
    }


    /**
     * @param class|string $class
     * @param string $cmd
     */
    public static function redirectNonAccess($class, string $cmd = ""): void
    {
        global $DIC;

        $ctrl = $DIC->ctrl();

        ilUtil::sendFailure($DIC->language()->txt("permission_denied"), true);

        if (is_object($class)) {
            $ctrl->clearParameters($class);
            $ctrl->redirect($class, $cmd);
        } else {
            $ctrl->clearParametersByClass($class);
            $ctrl->redirectByClass($class, $cmd);
        }
    }


    /**
     * @param int|null $ref_id
     *
     * @return bool
     */
    public static function hasVisibleAccess(int $ref_id = null): bool
    {
        return self::checkAccess("visible", "visible", $ref_id);
    }


    /**
     * @param int|null $ref_id
     *
     * @return bool
     */
    public static function hasReadAccess(int $ref_id = null): bool
    {
        if(is_null($ref_id)) {
            return true;
        }
        return self::checkAccess("read", "read", $ref_id);
    }


    /**
     * @param int|null $ref_id
     *
     * @return bool
     */
    public static function hasWriteAccess(int $ref_id = null): bool
    {
        if(is_null($ref_id)) {
            return true;
        }
        return self::checkAccess("write", "write", $ref_id);
    }


    /**
     * @param int|null $ref_id
     *
     * @return bool
     */
    public static function hasDeleteAccess(int $ref_id = null)
    {
        return self::checkAccess("delete", "delete", $ref_id);
    }


    /**
     * @param int|null $ref_id
     *
     * @return bool
     */
    public static function hasEditPermissionAccess(int $ref_id = null): bool
    {
        return self::checkAccess("edit_permission", "edit_permission", $ref_id);
    }
}