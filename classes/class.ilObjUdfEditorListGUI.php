<?php
require_once __DIR__ . "/../vendor/autoload.php";

class ilObjUdfEditorListGUI extends ilObjectPluginListGUI
{
    protected int $obj_id = 0;
    function getGuiClass(): string
    {
        return ilObjUdfEditorGUI::class;
    }

    function initCommands(): array
    {
        $this->timings_enabled = true;
        $this->subscribe_enabled = false;
        $this->payment_enabled = false;
        $this->link_enabled = false;
        $this->info_screen_enabled = true;
        $this->delete_enabled = true;
        $this->cut_enabled = true;
        $this->copy_enabled = true;
        $this->comments_enabled = false;
        $this->tags_enabled = false;
        $this->notes_enabled = false;

        $commands = [
            [
                "permission" => "read",
                "cmd"        => ilObjUdfEditorGUI::CMD_INDEX,
                "default"    => true,
            ],
            [
                "permission" => "write",
                "cmd"        => ilObjUdfEditorGUI::CMD_SETTINGS,
                "lang_var"   => 'settings'
            ]
        ];

        return $commands;
    }

    function initType(): void
    {
        $this->setType(ilUdfEditorPlugin::PLUGIN_ID);
    }

    /**
     * get all alert properties
     *
     * @return array
     */
    public function getAlertProperties(): array
    {
        $alert = array();
        foreach ((array) $this->getCustomProperties(array()) as $prop) {
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
    public function getCustomProperties(array $prop): array
    {
        global $DIC;
        $props = parent::getCustomProperties(array());

        if($this->obj_id !== 0){
            $settings = xudfSetting::find($this->obj_id);
            if (!$settings->isOnline()) {
                $props[] = array(
                    'alert'               => true,
                    'newline'             => true,
                    'property'            => 'Status',
                    'value'               => 'Offline',
                    'propertyNameVisible' => true
                );
            }
        }


        return $props;
    }
}