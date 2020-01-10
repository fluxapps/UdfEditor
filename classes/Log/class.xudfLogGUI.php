<?php

use srag\DIC\UdfEditor\Exception\DICException;

/**
 * Class xudfLogGUI
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy xudfLogGUI: ilObjUdfEditorGUI
 */
class xudfLogGUI extends xudfGUI
{

    /**
     * @inheritDoc
     * @throws DICException
     */
    protected function index()
    {
        $table = new xudfLogTableGUI($this, self::CMD_STANDARD);
        $this->tpl->setContent($table->getHTML());
    }


    /**
     * @throws DICException
     */
    protected function applyFilter()
    {
        $table = new xudfLogTableGUI($this, self::CMD_STANDARD);
        $table->writeFilterToSession();
        $table->resetOffset();
        $this->index();
    }


    /**
     * @throws DICException
     */
    protected function resetFilter()
    {
        $table = new xudfLogTableGUI($this, self::CMD_STANDARD);
        $table->resetFilter();
        $table->resetOffset();
        $this->index();
    }
}