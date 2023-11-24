<?php
class xudfPageObjectConfig extends ilPageConfig
{
    function init(): void
    {
        // config
        $this->setPreventHTMLUnmasking(true);
        $this->setEnableInternalLinks(false);
        $this->setEnableWikiLinks(false);
        $this->setEnableActivation(false);
    }
}