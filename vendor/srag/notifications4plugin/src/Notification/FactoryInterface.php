<?php

namespace srag\Notifications4Plugin\UdfEditor\Notification;

use srag\Notifications4Plugin\UdfEditor\Notification\Form\FormBuilder;
use srag\Notifications4Plugin\UdfEditor\Notification\Table\TableBuilder;
use stdClass;

/**
 * Interface FactoryInterface
 *
 * @package srag\Notifications4Plugin\UdfEditor\Notification
 */
interface FactoryInterface
{

    /**
     * @param stdClass $data
     *
     * @return NotificationInterface
     */
    public function fromDB(stdClass $data) : NotificationInterface;


    /**
     * @param NotificationCtrl      $parent
     * @param NotificationInterface $notification
     *
     * @return FormBuilder
     */
    public function newFormBuilderInstance(NotificationCtrl $parent, NotificationInterface $notification) : FormBuilder;


    /**
     * @return NotificationInterface
     */
    public function newInstance() : NotificationInterface;


    /**
     * @param NotificationsCtrl $parent
     *
     * @return TableBuilder
     */
    public function newTableBuilderInstance(NotificationsCtrl $parent) : TableBuilder;
}
