<?php

class xudfFormConfigurationTableGUI extends ilTable2GUI
{
    const PLUGIN_CLASS_NAME = ilUdfEditorPlugin::class;
    protected ilPlugin|ilUdfEditorPlugin $pl;


    /**
     * xudfFormConfigurationTableGUI constructor.
     *
     * @param $parent_gui
     * @param $parent_cmd
     * @throws ilCtrlException|arException
     */
    public function __construct($parent_gui, $parent_cmd)
    {
        global $DIC;
        /** @var $component_factory ilComponentFactory */
        $component_factory = $DIC['component.factory'];
        $this->pl  = $component_factory->getPlugin(ilUdfEditorPlugin::PLUGIN_ID);
        $this->main_tpl = $DIC->ui()->mainTemplate();
        $this->lng = $DIC->language();

        parent::__construct($parent_gui, $parent_cmd);

        $this->setFormAction($this->ctrl->getFormAction($parent_gui));
        $this->setRowTemplate($this->pl->getDirectory() . '/templates/default/tpl.form_configuration_table_row.html');

        $this->main_tpl->addJavaScript($this->pl->getDirectory() . '/templates/default/sortable.js');
        $this->main_tpl->addJavaScript($this->pl->getDirectory() . '/templates/default/waiter.js');
        $this->main_tpl->addCss($this->pl->getDirectory() . '/templates/default/waiter.css');
        $this->main_tpl->addOnLoadCode("xoctWaiter.init();");

       $base_link = $this->ctrl->getLinkTarget($parent_gui, xudfFormConfigurationGUI::CMD_REORDER, '', true);
        $this->main_tpl->addOnLoadCode("xudf = {'base_link': '$base_link'};");

       $this->initColumns();
       $this->setData(xudfContentElement::where(['obj_id' => ilObjUdfEditor::_lookupObjectId(filter_input(INPUT_GET, 'ref_id'))])->orderBy('sort')->getArray());
    }


    /**
     * @throws DICException
     */
    protected function initColumns()
    {
        $this->addColumn('', '', 10, true);
        $this->addColumn($this->lng->txt('title'), 'title', 50);
        $this->addColumn($this->lng->txt('description'), 'description', 100);
        $this->addColumn($this->lng->txt('type'), 'type', 30);
        $this->addColumn($this->pl->txt('udf_type'), 'udf_type', 30);
        $this->addColumn($this->pl->txt('is_required'), 'is_required', 30);
        $this->addColumn('', '', 10, true);
    }


    /**
     * @param array $a_set
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
                : ($udf_definition['field_name'] ?: $this->pl->txt('field_not_found')));
        $this->tpl->setVariable('DESCRIPTION', $a_set['description']);
        $this->tpl->setVariable('TYPE', $a_set['is_separator'] ? 'Separator' : $this->pl->txt('udf_field'));

        $this->tpl->setVariable('UDF_TYPE',
            $a_set['is_separator'] ? '&nbsp'
                : ($udf_definition['field_type'] ? $this->pl->txt('udf_field_type_' . $udf_definition['field_type']) : $this->pl->txt('field_not_found')));

        if ($a_set['is_separator']) {
            $udf_required = '&nbsp';
        } else {
            if ($a_set['is_required'] == 1) {
                $udf_required = '<img width="20px" height="20px" src="./templates/default/images/icon_ok.svg">';
            } else {
                $udf_required = '<img width="20px" height="20px" src="./templates/default/images/icon_not_ok.svg">';
            }
        }
        $this->tpl->setVariable('IS_REQUIRED', $udf_required);
        $this->tpl->setVariable('ACTIONS', $this->buildActions($a_set['id']));
    }
    protected function showMissingUdfMessage(): void
    {
        static $already_shown;
        if (!$already_shown) {
            $this->tpl->setOnScreenMessage("failure", $this->pl->txt('msg_missing_udf'), true);
            $already_shown = true;
        }
    }

    /**
     * @throws ilCtrlException
     * @throws JsonException
     */
    protected function buildActions($id): string
    {
        $actions = new ilAdvancedSelectionListGUI();
        $actions->setListTitle($this->lng->txt('actions'));
        $this->ctrl->setParameter($this->parent_obj, 'element_id', $id);

        $actions->addItem($this->lng->txt('edit'), 'edit', $this->ctrl->getLinkTarget($this->parent_obj, xudfFormConfigurationGUI::CMD_EDIT));
        $actions->addItem($this->lng->txt('delete'), 'delete', $this->ctrl->getLinkTarget($this->parent_obj, xudfFormConfigurationGUI::CMD_DELETE));

        return $actions->getHTML();
    }
}