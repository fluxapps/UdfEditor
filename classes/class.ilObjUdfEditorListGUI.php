<?php
require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Class ilObjUdfEditorListGUI
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class ilObjUdfEditorListGUI extends ilObjectPluginListGUI {

    function getGuiClass() {
        return ilObjUdfEditorGUI::class;
    }

    function initCommands() {
        $this->timings_enabled = false;
        $this->subscribe_enabled = false;
        $this->payment_enabled = false;
        $this->link_enabled = false;
        $this->info_screen_enabled = true;
        $this->delete_enabled = true;
        $this->cut_enabled = true;
        $this->copy_enabled = false;
        $this->comments_enabled = false;
        $this->tags_enabled = false;
        $this->notes_enabled = false;

        $commands = [
            [
                "permission" => "read",
                "cmd" => ilObjUdfEditorGUI::CMD_INDEX,
                "default" => true,
            ],
            [
                "permission" => "write",
                "cmd" => ilObjUdfEditorGUI::CMD_SETTINGS,
                "lang_var" => 'settings'
            ]
        ];

        return $commands;
    }

    function initType() {
        return ilUdfEditorPlugin::PLUGIN_ID;
    }

    /**
     * get all alert properties
     *
     * @return array
     */
    public function getAlertProperties() {
        $alert = array();
        foreach ((array)$this->getCustomProperties(array()) as $prop) {
            if ($prop['alert'] == true) {
                $alert[] = $prop;
            }
        }

        return $alert;
    }

    /**
     * Get item properties
     *
     * @return    array        array of property arrays:
     *                        'alert' (boolean) => display as an alert property (usually in red)
     *                        'property' (string) => property name
     *                        'value' (string) => property value
     */
    public function getCustomProperties($a_prop) {
        $props = parent::getCustomProperties(array());

        $settings = xudfSetting::find($this->obj_id);
        if (!$settings->isOnline()) {
            $props[] = array(
                'alert' => true,
                'newline' => true,
                'property' => 'Status',
                'value' => 'Offline',
                'propertyNameVisible' => true
            );
        }


        return $props;
    }


}