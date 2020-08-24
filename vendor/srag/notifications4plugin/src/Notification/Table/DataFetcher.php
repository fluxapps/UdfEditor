<?php

namespace srag\Notifications4Plugin\UdfEditor\Notification\Table;

use srag\DataTableUI\UdfEditor\Component\Data\Data;
use srag\DataTableUI\UdfEditor\Component\Data\Row\RowData;
use srag\DataTableUI\UdfEditor\Component\Settings\Settings;
use srag\DataTableUI\UdfEditor\Implementation\Data\Fetcher\AbstractDataFetcher;
use srag\Notifications4Plugin\UdfEditor\Notification\NotificationInterface;
use srag\Notifications4Plugin\UdfEditor\Utils\Notifications4PluginTrait;

/**
 * Class DataFetcher
 *
 * @package srag\Notifications4Plugin\UdfEditor\Notification\Table
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class DataFetcher extends AbstractDataFetcher
{

    use Notifications4PluginTrait;

    /**
     * @inheritDoc
     */
    public function fetchData(Settings $settings) : Data
    {
        return self::dataTableUI()->data()->data(array_map(function (NotificationInterface $notification
        ) : RowData {
            return self::dataTableUI()->data()->row()->getter($notification->getId(), $notification);
        }, self::notifications4plugin()->notifications()->getNotifications($settings)),
            self::notifications4plugin()->notifications()->getNotificationsCount());
    }
}
