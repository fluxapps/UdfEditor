<?php

use srag\DIC\UdfEditor\DICTrait;
use srag\DIC\UdfEditor\Exception\DICException;

/**
 * Class xudfFormConfigurationTableGUI
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class xudfFormConfigurationTableGUI extends ilTable2GUI
{

    use DICTrait;
    const PLUGIN_CLASS_NAME = ilUdfEditorPlugin::class;
    /**
     * @var ilUdfEditorPlugin
     */
    protected $pl;


    /**
     * xudfFormConfigurationTableGUI constructor.
     *
     * @param $parent_gui
     * @param $parent_cmd
     *
     * @throws DICException
     * @throws arException
     */
    public function __construct($parent_gui, $parent_cmd)
    {
        global $DIC;

        /** @var $component_factory ilComponentFactory */
        $component_factory = $DIC['component.factory'];
        /** @var $plugin ilUdfEditorPlugin */
        $this->pl  = $component_factory->getPlugin(ilUdfEditorPlugin::PLUGIN_ID);

        parent::__construct($parent_gui, $parent_cmd);

        $this->setFormAction(self::dic()->ctrl()->getFormAction($parent_gui));
        $this->setRowTemplate(self::plugin()->directory() . '/templates/default/tpl.form_configuration_table_row.html');

        self::dic()->ui()->mainTemplate()->addJavaScript(self::plugin()->directory() . '/templates/default/sortable.js');
        self::dic()->ui()->mainTemplate()->addJavaScript(self::plugin()->directory() . '/templates/default/waiter.js');
        self::dic()->ui()->mainTemplate()->addCss(self::plugin()->directory() . '/templates/default/waiter.css');
        self::dic()->ui()->mainTemplate()->addOnLoadCode("xoctWaiter.init();");

        $base_link = self::dic()->ctrl()->getLinkTarget($parent_gui, xudfFormConfigurationGUI::CMD_REORDER, '', true);
        self::dic()->ui()->mainTemplate()->addOnLoadCode("xudf = {'base_link': '$base_link'};");

        $this->initColumns();
        $this->setData(xudfContentElement::where(['obj_id' => ilObjUdfEditor::_lookupObjectId(filter_input(INPUT_GET, 'ref_id'))])->orderBy('sort')->getArray());
    }


    /**
     * @throws DICException
     */
    protected function initColumns()
    {
        $this->addColumn('', '', 10, true);
        $this->addColumn(self::dic()->language()->txt('title'), 'title', 50);
        $this->addColumn(self::dic()->language()->txt('description'), 'description', 100);
        $this->addColumn(self::dic()->language()->txt('type'), 'type', 30);
        $this->addColumn(self::plugin()->translate('udf_type'), 'udf_type', 30);
        $this->addColumn(self::plugin()->translate('is_required'), 'is_required', 30);
        $this->addColumn('', '', 10, true);
    }


    /**
     * @param array $a_set
     *
     * @throws DICException
     */
    protected function fillRow(array $a_set): void
    {
        $udf_definition = ilUserDefinedFields::_getInstance()->getDefinition($a_set['udf_field']);

        if (!$a_set['is_separator'] && !$udf_definition) {
            $this->showMissingUdfMessage();
        }

        $this->tpl->setVariable('ID', $a_set['id']);
        $this->tpl->setVariable('TITLE',
            $a_set['is_separator'] ?
                $a_set['title']
                : ($udf_definition['field_name'] ?: self::plugin()->translate('field_not_found')));
        $this->tpl->setVariable('DESCRIPTION', $a_set['description']);
        $this->tpl->setVariable('TYPE', $a_set['is_separator'] ? 'Separator' : self::plugin()->translate('udf_field'));

        $this->tpl->setVariable('UDF_TYPE',
            $a_set['is_separator'] ? '&nbsp'
                : ($udf_definition['field_type'] ? self::plugin()->translate('udf_field_type_' . $udf_definition['field_type']) : self::plugin()->translate('field_not_found')));

        if ($a_set['is_separator']) {
            $udf_required = '&nbsp';
        } else {
            if ($a_set['is_required'] == 1) {
                $udf_required = '<img src="./templates/default/images/icon_ok.svg">';
            } else {
                $udf_required = '<img src="./templates/default/images/icon_not_ok.svg">';
            }
        }

        $this->tpl->setVariable('IS_REQUIRED', $udf_required);

        $this->tpl->setVariable('ACTIONS', $this->buildActions($a_set['id']));
    }


    /**
     * @throws DICException
     */
    protected function showMissingUdfMessage()
    {
        static $already_shown;
        if (!$already_shown) {
            ilUtil::sendFailure(self::plugin()->translate('msg_missing_udf'), true);
            $already_shown = true;
        }
    }


    /**
     * @param $id
     *
     * @return string
     */
    protected function buildActions($id)
    {
        $actions = new ilAdvancedSelectionListGUI();
        $actions->setListTitle(self::dic()->language()->txt('actions'));
        self::dic()->ctrl()->setParameter($this->parent_obj, 'element_id', $id);

        $actions->addItem(self::dic()->language()->txt('edit'), 'edit', self::dic()->ctrl()->getLinkTarget($this->parent_obj, xudfFormConfigurationGUI::CMD_EDIT));
        $actions->addItem(self::dic()->language()->txt('delete'), 'delete', self::dic()->ctrl()->getLinkTarget($this->parent_obj, xudfFormConfigurationGUI::CMD_DELETE));

        return $actions->getHTML();
    }
}