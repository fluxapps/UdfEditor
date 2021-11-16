<?php

namespace srag\DIC\UdfEditor\Plugin;

/**
 * Interface Pluginable
 *
 * @package srag\DIC\UdfEditor\Plugin
 */
interface Pluginable
{

    /**
     * @return PluginInterface
     */
    public function getPlugin() : PluginInterface;


    /**
     * @param PluginInterface $plugin
     *
     * @return static
     */
    public function withPlugin(PluginInterface $plugin)/*: static*/ ;
}
