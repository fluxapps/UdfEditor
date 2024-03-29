<?php

namespace srag\Notifications4Plugin\UdfEditor;

use srag\DIC\UdfEditor\Plugin\Pluginable;
use srag\Notifications4Plugin\UdfEditor\Notification\RepositoryInterface as NotificationRepositoryInterface;
use srag\Notifications4Plugin\UdfEditor\Parser\RepositoryInterface as ParserRepositoryInterface;
use srag\Notifications4Plugin\UdfEditor\Sender\RepositoryInterface as SenderRepositoryInterface;

/**
 * Interface RepositoryInterface
 *
 * @package srag\Notifications4Plugin\UdfEditor
 */
interface RepositoryInterface extends Pluginable
{

    /**
     *
     */
    public function dropTables() : void;


    /**
     * @return array
     */
    public function getPlaceholderTypes() : array;


    /**
     * @return string
     */
    public function getTableNamePrefix() : string;


    /**
     *
     */
    public function installLanguages() : void;


    /**
     *
     */
    public function installTables() : void;


    /**
     * @return NotificationRepositoryInterface
     */
    public function notifications() : NotificationRepositoryInterface;


    /**
     * @return ParserRepositoryInterface
     */
    public function parser() : ParserRepositoryInterface;


    /**
     * @return SenderRepositoryInterface
     */
    public function sender() : SenderRepositoryInterface;


    /**
     * @param array $placeholder_types
     *
     * @return self
     */
    public function withPlaceholderTypes(array $placeholder_types) : self;


    /**
     * @param string $table_name_prefix
     *
     * @return self
     */
    public function withTableNamePrefix(string $table_name_prefix) : self;
}
