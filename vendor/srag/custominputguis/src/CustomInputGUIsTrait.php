<?php

namespace srag\CustomInputGUIs\UdfEditor;

/**
 * Trait CustomInputGUIsTrait
 *
 * @package srag\CustomInputGUIs\UdfEditor
 */
trait CustomInputGUIsTrait
{

    /**
     * @return CustomInputGUIs
     */
    protected static final function customInputGUIs() : CustomInputGUIs
    {
        return CustomInputGUIs::getInstance();
    }
}
