<?php

namespace srag\DataTableUI\UdfEditor\Component\Format\Browser\Filter;

use srag\CustomInputGUIs\UdfEditor\FormBuilder\FormBuilder;
use srag\DataTableUI\UdfEditor\Component\Format\Browser\BrowserFormat;
use srag\DataTableUI\UdfEditor\Component\Settings\Settings;
use srag\DataTableUI\UdfEditor\Component\Table;

/**
 * Interface Factory
 *
 * @package srag\DataTableUI\UdfEditor\Component\Format\Browser\Filter
 */
interface Factory
{

    /**
     * @param BrowserFormat $parent
     * @param Table         $component
     * @param Settings      $settings
     *
     * @return FormBuilder
     */
    public function formBuilder(BrowserFormat $parent, Table $component, Settings $settings) : FormBuilder;
}
