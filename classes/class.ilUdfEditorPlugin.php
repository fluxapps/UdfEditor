<?php

require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Class ilUdfEditorPlugin
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class ilUdfEditorPlugin extends ilRepositoryObjectPlugin {

    const PLUGIN_ID = 'xudf';
    const PLUGIN_CLASS_NAME = self::class;

    protected $udfeditor_container;

    function getPluginName(): string {
        return 'UdfEditor';
    }

    /**
     * @return ilUdfEditorPlugin
     */
    public static function getInstance(): ilUdfEditorPlugin
    {
        if (!isset(self::$instance)) {
            global $DIC;

            /** @var $component_factory ilComponentFactory */
            $component_factory = $DIC['component.factory'];
            /** @var $plugin ilUdfEditorPlugin */
            $plugin  = $component_factory->getPlugin(ilUdfEditorPlugin::PLUGIN_ID);

            self::$instance = $plugin;
        }

        return self::$instance;
    }


    /**
     *
     */
    public static function initNotifications()/*:void*/
    {
       /* if (!self::$init_notifications) {
            self::$init_notifications = true;

            self::notifications4plugin()->withTableNamePrefix(self::PLUGIN_ID)->withPlugin(self::plugin())->withPlaceholderTypes([
                "object" => "object " . ilObjUdfEditor::class,
                "user" => "object " . ilObjUser::class,
                "user_defined_data" => "array"
            ]);
        }
       */
    }
    protected static ?ilUdfEditorPlugin $instance = NULL;

    /**
     * @return bool
     */
    public function allowCopy(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function __construct(
        ilDBInterface $db,
        ilComponentRepositoryWrite $component_repository,
        string $id
    ) {
        parent::__construct($db, $component_repository, $id);
    }

    protected function init():void
    {

    }


    protected function uninstallCustom(): void {
       $this->db->dropTable(xudfSetting::DB_TABLE_NAME, false);
       $this->db->dropTable(xudfContentElement::DB_TABLE_NAME, false);

        $sequences = [
            xudfSetting::DB_TABLE_NAME,
            xudfContentElement::DB_TABLE_NAME
        ];
        foreach ($sequences as $sequence) {
            try {
                $this->db->dropSequence($sequence);
            }catch (Exception $e){
                //ignore
            }
        }

        //$this->db->manipulateF('DELETE FROM copg_pobj_def WHERE component=%s', [ 'text' ], [ 'Customizing/global/plugins/Services/Repository/RepositoryObject/UdfEditor' ]);
    }
}