<?php

namespace Avltree\Bee\Connection;

use Avltree\Bee\Exception\ConnectionException;

/**
 * Interface for handling IRC server connections.
 *
 * @package Avltree\Bee\Connection
 */
interface ConnectionInterface
{
    /**
     * Establishes the connection to the server.
     *
     * @throws ConnectionException when the connection fails.
     */
    public function start();

    /**
     * Reads data from the socket.
     *
     * @return string|null If no data is available, returns null, otherwise it returns a string.
     * @throws ConnectionException when data reading fails.
     */
    public function readData(): ?string;

    /**
     * Use to check if the connection is currently established.
     *
     * @return bool
     */
    public function isConnected(): bool;

    /**
     * Sends a message to the server.
     *
     * @param string $name
     * @param string|null $payload
     * @throws ConnectionException when sending data fails.
     */
    public function sendMessage(string $name, ?string $payload = null);
}
