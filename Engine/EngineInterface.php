<?php

namespace Avltree\Bee\Engine;

/**
 * Engine is used to process and handle messages retrieved from the IRC server.
 *
 * @package Avltree\Bee\Engine
 */
interface EngineInterface
{
    /**
     * Runs the engine.
     *
     * @return mixed
     */
    public function run();

    /**
     * Stops the engine.
     *
     * @return mixed
     */
    public function stop();
}
