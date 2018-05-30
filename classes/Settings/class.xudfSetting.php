<?php

/**
 * Class xudfSetting
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class xudfSetting extends ActiveRecord {

    public function getConnectorContainerName() {
        return 'xudf_setting';
    }

    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     * @con_is_primary   true
     */
    protected $obj_id;
    /**
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     * @con_is_notnull   true
     */
    protected $is_online = false;
    /**
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     * @con_is_notnull   true
     */
    protected $show_info_tab = false;

    /**
     * @return int
     */
    public function getObjId() {
        return $this->obj_id;
    }

    /**
     * @param int $obj_id
     */
    public function setObjId($obj_id) {
        $this->obj_id = $obj_id;
    }

    /**
     * @return bool
     */
    public function isOnline() {
        return $this->is_online;
    }

    /**
     * @param bool $is_online
     */
    public function setIsOnline($is_online) {
        $this->is_online = $is_online;
    }

    /**
     * @return bool
     */
    public function isShowInfoTab() {
        return $this->show_info_tab;
    }

    /**
     * @param bool $show_info_tab
     */
    public function setShowInfoTab($show_info_tab) {
        $this->show_info_tab = $show_info_tab;
    }


}