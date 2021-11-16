<?php

use srag\CustomInputGUIs\UdfEditor\PropertyFormGUI\PropertyFormGUI;
use srag\CustomInputGUIs\UdfEditor\TableGUI\TableGUI;
use srag\DIC\UdfEditor\Exception\DICException;

/**
 * Class xudfLogTableGUI
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class xudfLogTableGUI extends TableGUI
{

    const ID_PREFIX = 'xudf_log_table_';
    const PLUGIN_CLASS_NAME = ilUdfEditorPlugin::class;
    const ROW_TEMPLATE = 'tpl.log_table_row.html';
    /**
     * @var xudfLogGUI
     */
    protected $parent_obj;


    /**
     * xudfLogTableGUI constructor.
     *
     * @param $parent
     * @param $parent_cmd
     *
     * @throws DICException
     */
    public function __construct($parent, $parent_cmd)
    {
        $this->parent_obj = $parent;
        parent::__construct($parent, $parent_cmd);
        self::dic()->ui()->mainTemplate()->addCss(self::plugin()->directory() . '/templates/default/log_table.css');
    }


    /**
     * @param string       $column
     * @param array|object $row
     * @param int          $format
     *
     * @return string|void
     */
    protected function getColumnValue(string $column, /*array*/ $row, int $format = self::DEFAULT_FORMAT) : string
    {
    }


    /**
     * @return array
     */
    protected function getSelectableColumns2() : array
    {
        return [];
    }


    /**
     * @throws DICException
     */
    protected function initColumns() : void
    {
        $this->addColumn(self::plugin()->translate('values'));
        $this->addColumn(self::dic()->language()->txt('user'), 'user');
        $this->addColumn(self::dic()->language()->txt('date'), 'timestamp');
    }


    /**
     *
     * @throws Exception
     */
    protected function initData() : void
    {
        $filter_values = $this->getFilterValues();
        $filter_user = $filter_values['user'];

        $where = xudfLogEntry::where(['obj_id' => $this->parent_obj->getObjId()]);
        if ($filter_user != '') {
            $where = $where->where(['usr_id' => $filter_user]);
        }
        $this->setData($where->getArray());
    }


    /**
     *
     */
    protected function initFilterFields() : void
    {
        $this->filter_fields = [
            "user" => [
                PropertyFormGUI::PROPERTY_CLASS   => ilSelectInputGUI::class,
                PropertyFormGUI::PROPERTY_OPTIONS => $this->getUserFilterOptions()
            ]
        ];
    }


    /**
     *
     */
    protected function initId() : void
    {
        $this->setId(self::ID_PREFIX . $this->parent_obj->getObjId());
    }


    /**
     *
     */
    protected function initTitle() : void
    {
        $this->setTitle(self::dic()->language()->txt('history'));
    }


    /**
     * @param array $row
     *
     * @throws DICException
     */
    protected function fillRow($row) : void
    {
        $this->tpl->setVariable('VALUES', $this->formatValues($row['values']));
        $this->tpl->setVariable('USER', ilObjUser::_lookupFullname($row['usr_id']) . ', [' . ilObjUser::_lookupLogin($row['usr_id']) . ']');
        $this->tpl->setVariable('DATE', $row['timestamp']->get(IL_CAL_FKT_DATE, 'd.m.Y H:i:s'));
    }


    /**
     * @param array $values
     *
     * @return string
     * @throws DICException
     */
    protected function formatValues(array $values) : string
    {
        // this should be a template, but i'm too lazy
        $string = '<table class="xudf_log_values">';
        $string .= '<tr><th>' . self::plugin()->translate('udf_field') . '</th><th>' . self::dic()->language()->txt('value') . '</th></tr>';
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
     * @throws DICException
     */
    protected function getUserFilterOptions() : array
    {
        $result = self::dic()->database()->query(
            'SELECT DISTINCT(usr_id) FROM ' . xudfLogEntry::TABLE_NAME
        );
        $options = ['' => '-'];
        while ($rec = self::dic()->database()->fetchAssoc($result)) {
            $options[$rec['usr_id']] = ilObjUser::_lookupFullname($rec['usr_id']) . ', [' . ilObjUser::_lookupLogin($rec['usr_id']) . ']';
        }

        return $options;
    }
}