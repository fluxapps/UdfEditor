<?php

namespace srag\CustomInputGUIs\UdfEditor\HiddenInputGUI;

use ilHiddenInputGUI;
use srag\CustomInputGUIs\UdfEditor\Template\Template;
use srag\DIC\UdfEditor\DICTrait;

/**
 * Class HiddenInputGUI
 *
 * @package srag\CustomInputGUIs\UdfEditor\HiddenInputGUI
 */
class HiddenInputGUI extends ilHiddenInputGUI
{

    use DICTrait;

    /**
     * HiddenInputGUI constructor
     *
     * @param string $a_postvar
     */
    public function __construct(string $a_postvar = "")
    {
        parent::__construct($a_postvar);
    }


    /**
     * @return string
     */
    public function render() : string
    {
        $tpl = new Template("Services/Form/templates/default/tpl.property_form.html", true, true);

        $this->insert($tpl);

        return self::output()->getHTML($tpl);
    }
}
