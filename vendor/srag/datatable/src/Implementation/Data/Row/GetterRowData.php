<?php

namespace srag\DataTableUI\UdfEditor\Implementation\Data\Row;

use srag\CustomInputGUIs\UdfEditor\PropertyFormGUI\Items\Items;

/**
 * Class GetterRowData
 *
 * @package srag\DataTableUI\UdfEditor\Implementation\Data\Row
 */
class GetterRowData extends AbstractRowData
{

    /**
     * @inheritDoc
     */
    public function __invoke(string $key)
    {
        return Items::getter($this->getOriginalData(), $key);
    }
}
