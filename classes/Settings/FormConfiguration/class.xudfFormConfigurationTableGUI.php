<?php

/**
 * Class xudfFormConfigurationTableGUI
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class xudfFormConfigurationTableGUI extends ilTable2GUI {

    /**
     * @var ilCtrl
     */
    protected $ctrl;
    /**
     * @var ilTemplate
     */
    protected $tpl_global;
    /**
     * @var ilUdfEditorPlugin
     */
    protected $pl;


    /**
     * xudfFormConfigurationTableGUI constructor.
     * @param $parent_gui
     * @param $parent_cmd
     */
    public function __construct($parent_gui, $parent_cmd) {
        global $DIC;
        $ilCtrl = $DIC['ilCtrl'];
        $tpl = $DIC['tpl'];
        $this->ctrl = $ilCtrl;
        $this->pl = ilUdfEditorPlugin::getInstance();
        $this->tpl_global = $tpl;

        parent::__construct($parent_gui, $parent_cmd);

        $this->setFormAction($this->ctrl->getFormAction($parent_gui));
        $this->setRowTemplate($this->pl->getDirectory() . '/templates/default/tpl.form_configuration_table_row.html');

        $this->tpl_global->addJavaScript($this->pl->getDirectory() . '/templates/default/sortable.js');
        $this->tpl_global->addJavaScript($this->pl->getDirectory() . '/templates/default/waiter.js');
        $this->tpl_global->addCss($this->pl->getDirectory() . '/templates/default/waiter.css');
        $this->tpl_global->addOnLoadCode("xoctWaiter.init();");

        $base_link = $this->ctrl->getLinkTarget($parent_gui, xudfFormConfigurationGUI::CMD_REORDER, '', true);
        $this->tpl_global->addOnLoadCode("xudf = {'base_link': '$base_link'};");

        $this->initColumns();
        $this->setData(xudfContentElement::orderBy('sort')->getArray());
    }

    protected function initColumns() {
        $this->addColumn('', '', 10, true);
        $this->addColumn($this->lng->txt('title'), 'title', 50);
        $this->addColumn($this->lng->txt('description'),'description', 100);
        $this->addColumn($this->lng->txt('type'),'type', 30);
        $this->addColumn($this->pl->txt('udf_type'),'udf_type', 30);
        $this->addColumn($this->pl->txt('udf_required'),'udf_required', 30);
        $this->addColumn('','', 10, true);
    }

    protected function fillRow($a_set) {
        $udf_definition = ilUserDefinedFields::_getInstance()->getDefinition($a_set['udf_field']);

        $this->tpl->setVariable('ID', $a_set['id']);
        $this->tpl->setVariable('TITLE', $udf_definition ? $udf_definition['field_name'] : $a_set['title']);
        $this->tpl->setVariable('DESCRIPTION', $a_set['description']);
        $this->tpl->setVariable('TYPE', $a_set['is_separator'] ? 'Separator' : $this->pl->txt('udf_field'));

        $this->tpl->setVariable('UDF_TYPE', $udf_definition ? $this->pl->txt('udf_field_type_' . $udf_definition['field_type']) : '&nbsp');

        if ($a_set['is_separator']) {
            $udf_required = '&nbsp';
        } else if ($udf_definition['required'] == 1) {
            $udf_required = '<img src="./templates/default/images/icon_ok.svg">';
        } else {
            $udf_required = '<img src="./templates/default/images/icon_not_ok.svg">';
        }

        $this->tpl->setVariable('UDF_REQUIRED', $udf_required);

        $this->tpl->setVariable('ACTIONS', $this->buildActions($a_set['id']));
    }

    protected function buildActions($id) {
        $actions = new ilAdvancedSelectionListGUI();
        $actions->setListTitle($this->lng->txt('actions'));
        $this->ctrl->setParameter($this->parent_obj, 'element_id', $id);

        $actions->addItem($this->lng->txt('edit'), 'edit', $this->ctrl->getLinkTarget($this->parent_obj, xudfFormConfigurationGUI::CMD_EDIT));
        $actions->addItem($this->lng->txt('delete'), 'delete', $this->ctrl->getLinkTarget($this->parent_obj, xudfFormConfigurationGUI::CMD_DELETE));
        return $actions->getHTML();
    }


}