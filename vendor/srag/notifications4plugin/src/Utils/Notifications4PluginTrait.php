<?php

namespace srag\Notifications4Plugin\UdfEditor\Utils;

use srag\Notifications4Plugin\UdfEditor\Repository as Notifications4PluginRepository;
use srag\Notifications4Plugin\UdfEditor\RepositoryInterface as Notifications4PluginRepositoryInterface;

/**
 * Trait Notifications4PluginTrait
 *
 * @package srag\Notifications4Plugin\UdfEditor\Utils
 */
trait Notifications4PluginTrait
{

    /**
     * @return Notifications4PluginRepositoryInterface
     */
    protected static function notifications4plugin() : Notifications4PluginRepositoryInterface
    {
        return Notifications4PluginRepository::getInstance();
    }
}
