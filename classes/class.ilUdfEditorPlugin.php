<?php

require_once __DIR__ . "/../vendor/autoload.php";

use ILIAS\DI\Container;
use srag\CustomInputGUIs\UdfEditor\Loader\CustomInputGUIsLoaderDetector;
use srag\DIC\UdfEditor\DICTrait;
use srag\Notifications4Plugin\UdfEditor\Utils\Notifications4PluginTrait;

/**
 * Class ilUdfEditorPlugin
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class ilUdfEditorPlugin extends ilRepositoryObjectPlugin {

    const PLUGIN_ID = 'xudf';
    const PLUGIN_CLASS_NAME = self::class;

    /**
     * @var IContainer
     */
    protected $udfeditor_container;

    function getPluginName(): string {
        return 'UdfEditor';
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

    /**
     * @var ilUdfEditorPlugin
     */
    protected static $instance = NULL;


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
        global $DIC;
        parent::__construct($db, $component_repository, $id);

        // this plugin might be called by the cron-hook plugin, which allows
        // this class to be called in CLI context, where the ILIAS_HTTP_PATH
        // is not defined.
        if (!defined('ILIAS_HTTP_PATH')) {
            define('ILIAS_HTTP_PATH', ilUtil::_getHttpPath());
        }

        //$this->udfeditor_container = new ilH5PContainer($this, $DIC);
    }





    /**
     * @inheritDoc
     */
    protected function init():void
    {

    }


    /**
     * @inheritDoc
     */
    public function updateLanguages(/*array*/ $a_lang_keys = null)/*:void*/
    {
        parent::updateLanguages($a_lang_keys);
    }


    protected function uninstallCustom(): void {
        global $DIC;
        $DIC->database()->dropTable(xudfSetting::DB_TABLE_NAME, false);
        $DIC->database()->dropTable(xudfContentElement::DB_TABLE_NAME, false);
        $DIC->database()->manipulateF('DELETE FROM copg_pobj_def WHERE component=%s', [ 'text' ], [ 'Customizing/global/plugins/Services/Repository/RepositoryObject/UdfEditor' ]);
    }


    /**
     * @inheritDoc
     */
    public function exchangeUIRendererAfterInitialization(Container $dic) : Closure
    {
        return CustomInputGUIsLoaderDetector::exchangeUIRendererAfterInitialization();
    }
}