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
    use DICTrait;
    use Notifications4PluginTrait;
    const PLUGIN_ID = 'xudf';
    const PLUGIN_CLASS_NAME = self::class;

    function getPluginName() {
        return 'UdfEditor';
    }


    /**
     * @var bool
     */
    protected static $init_notifications = false;


    /**
     *
     */
    public static function initNotifications()/*:void*/
    {
        if (!self::$init_notifications) {
            self::$init_notifications = true;

            self::notifications4plugin()->withTableNamePrefix(self::PLUGIN_ID)->withPlugin(self::plugin())->withPlaceholderTypes([
                "object" => "object " . ilObjUdfEditor::class,
                "user" => "object " . ilObjUser::class,
                "user_defined_data" => "array"
            ]);
        }
    }

    /**
     * @var ilUdfEditorPlugin
     */
    protected static $instance = NULL;


    /**
     * @return bool
     */
    public function allowCopy()
    {
        return true;
    }


    /**
     * @return ilUdfEditorPlugin
     */
    public static function getInstance() {
        if (self::$instance === NULL) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @inheritDoc
     */
    protected function init()/*:void*/
    {
        self::initNotifications();
    }


    /**
     * @inheritDoc
     */
    public function updateLanguages(/*array*/ $a_lang_keys = null)/*:void*/
    {
        parent::updateLanguages($a_lang_keys);

        self::notifications4plugin()->installLanguages();
    }


    protected function uninstallCustom() {
        global $DIC;
        $DIC->database()->dropTable(xudfSetting::DB_TABLE_NAME, false);
        $DIC->database()->dropTable(xudfContentElement::DB_TABLE_NAME, false);
        $DIC->database()->manipulateF('DELETE FROM copg_pobj_def WHERE component=%s', [ 'text' ], [ 'Customizing/global/plugins/Services/Repository/RepositoryObject/UdfEditor' ]);
        self::notifications4plugin()->dropTables();
    }


    /**
     * @inheritDoc
     */
    public function exchangeUIRendererAfterInitialization(Container $dic) : Closure
    {
        return CustomInputGUIsLoaderDetector::exchangeUIRendererAfterInitialization();
    }
}