<?php

namespace srag\DataTableUI\UdfEditor\Component\Settings\Storage;

/**
 * Interface Factory
 *
 * @package srag\DataTableUI\UdfEditor\Component\Settings\Storage
 */
interface Factory
{

    /**
     * @return SettingsStorage
     */
    public function default() : SettingsStorage;
}
