<?php

namespace srag\Notifications4Plugin\UdfEditor\Parser;

/**
 * Interface FactoryInterface
 *
 * @package srag\Notifications4Plugin\UdfEditor\Parser
 */
interface FactoryInterface
{

    /**
     * @return twigParser
     */
    public function twig() : twigParser;
}
