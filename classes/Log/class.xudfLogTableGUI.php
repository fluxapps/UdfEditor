<?php


/**
 * Class xudfLogTableGUI
 */
class xudfLogTableGUI extends ilTable2GUI
{

    const ID_PREFIX = 'xudf_log_table_';
    const PLUGIN_CLASS_NAME = ilUdfEditorPlugin::class;
    const ROW_TEMPLATE = 'tpl.log_table_row.html';
    /**
     * @var xudfLogGUI
     */
    protected ?object $parent_obj;
    private ilUdfEditorPlugin $pl;
    private ilDBInterface $db;


    /**
     * xudfLogTableGUI constructor.
     *
     * @param $parent
     * @param $parent_cmd
     *
     */
    public function __construct($parent, $parent_cmd)
    {
        global $DIC;
        $this->main_tpl = $DIC->ui()->mainTemplate();
        $this->pl = ilUdfEditorPlugin::getInstance();
        $this->lng = $DIC->language();
        $this->db = $DIC->database();

        $this->parent_obj = $parent;
        parent::__construct($parent, $parent_cmd);
        $this->setFormAction($DIC->ctrl()->getFormAction($parent));

        $this->main_tpl->addCss($this->pl->getDirectory() . '/templates/default/log_table.css');

        $this->initTable();
    }


    /**
     * @param string $column
     * @param array|object $row
     * @param int $format
     *
     * @return string|void
     */
    protected function getColumnValue(string $column, /*array*/ $row, int $format = self::DEFAULT_FORMAT): string
    {
    }


    /**
     * @return array
     */
    protected function getSelectableColumns2(): array
    {
        return [];
    }

    protected function initColumns(): void
    {
        $this->addColumn($this->pl->txt('values'));
        $this->addColumn($this->lng->txt('user'), 'user');
        $this->addColumn($this->lng->txt('date'), 'timestamp');
    }

    /**
     * @throws ilTemplateException
     * @throws ilSystemStyleException
     */
    private function initTable(): void
    {
        if (!(strpos($this->parent_cmd, "applyFilter") === 0
            || strpos($this->parent_cmd, "resetFilter") === 0)
        ) {
            $this->tpl = new ilTemplate($this->tpl->lastTemplatefile, $this->tpl->removeUnknownVariables, $this->tpl->removeEmptyBlocks);

            //$this->initAction();

            $this->initTitle();

            $this->initFilter();

            $this->initData();

            $this->initColumns();

            //$this->initExport();

            $this->initRowTemplate();

            //$this->initCommands();
        } else {
            // Speed up, not init data on applyFilter or resetFilter, only filter
            $this->initFilter();
        }
    }

    private function initRowTemplate(): void
    {
        if ($this->checkRowTemplateConst()) {
            $this->setRowTemplate(static::ROW_TEMPLATE, $this->pl->getDirectory());
        } else {
            $dir = __DIR__;
            $dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);
            $this->setRowTemplate("table_row.html", $dir);
        }
    }

    /**
     * @return bool
     */
    private function checkRowTemplateConst(): bool
    {
        return (defined("static::ROW_TEMPLATE") && !empty(static::ROW_TEMPLATE));
    }


    /**
     * @throws Exception
     */
    protected function initData(): void
    {
        $filter_user = "";
        $filter_values = $this->getFilterValues();
        $filter_user = $filter_values['user'];

        $where = xudfLogEntry::where(['obj_id' => $this->parent_obj->getObjId()]);
        if ($filter_user != '') {
            $where = $where->where(['usr_id' => $filter_user]);
        }

        $this->setData($where->getArray());
    }

    protected function getFilterValues(): array
    {
        return (array)$this->filter;
    }

    public function initFilter(): void
    {
        // title
        $userSelect = new ilSelectInputGUI($this->lng->txt("user"), "user");
        $this->addFilterItem($userSelect);
        $userSelect->setOptions($this->getUserFilterOptions());
        $userSelect->readFromSession();    // get currenty value from session (always after addFilterItem())
        $this->filter["user"] = $userSelect->getValue();
    }

    protected function initFilterFields(): void
    {
        $this->filter_fields = [
            "user" => [
                PropertyFormGUI::PROPERTY_CLASS => ilSelectInputGUI::class,
                PropertyFormGUI::PROPERTY_OPTIONS => $this->getUserFilterOptions()
            ]
        ];
    }

    /**
     * @throws ilException
     */
    protected function initId(): void
    {
        $this->setId(self::ID_PREFIX . $this->parent_obj->getObjId());
    }

    protected function initTitle(): void
    {
        $this->setTitle($this->lng->txt('history'));
    }

    /**
     * @param array $row
     *
     */
    protected function fillRow($row): void
    {
        $this->tpl->setVariable('VALUES', $this->formatValues($row['values']));
        $this->tpl->setVariable('USER', ilObjUser::_lookupFullname($row['usr_id']) . ', [' . ilObjUser::_lookupLogin($row['usr_id']) . ']');
        $this->tpl->setVariable('DATE', $row['timestamp']->get(IL_CAL_FKT_DATE, 'd.m.Y H:i:s'));
    }

    /**
     * @param array $values
     *
     * @return string
     */
    protected function formatValues(array $values): string
    {
        // this should be a template, but i'm too lazy
        $string = '<table class="xudf_log_values">';
        $string .= '<tr><th>' . $this->pl->txt('udf_field') . '</th><th>' . $this->lng->txt('value') . '</th></tr>';
        foreach ($values as $title => $value) {
            $string .= '<tr>';
            $string .= '<td>' . $title . '</td>';
            $string .= '<td>' . $value . '</td>';
            $string .= '</tr>';
        }

        return $string . '</table>';
    }

    /**
     * @return array
     */
    protected function getUserFilterOptions(): array
    {
        $result = $this->db->query(
            'SELECT DISTINCT(usr_id) FROM ' . xudfLogEntry::TABLE_NAME
        );
        $options = ['' => '-'];
        while ($rec = $this->db->fetchAssoc($result)) {
            $options[$rec['usr_id']] = ilObjUser::_lookupFullname($rec['usr_id']) . ', [' . ilObjUser::_lookupLogin($rec['usr_id']) . ']';
        }

        return $options;
    }
}