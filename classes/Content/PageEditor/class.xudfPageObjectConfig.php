<?php

/**
 * Class xudfPageConfig
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class xudfPageConfig extends ilPageConfig  {

    /**
     * Init
     */
    function init()
    {
        // config
        $this->setPreventHTMLUnmasking(true);
        $this->setEnableInternalLinks(false);
        $this->setEnableWikiLinks(false);
        $this->setEnableActivation(false);
    }

}