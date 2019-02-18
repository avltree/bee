<?php

namespace Avltree\Bee\Connection;

use Avltree\Bee\Exception\ConnectionException;

/**
 * Class used to connect to a IRC server using built-in PHP socket implementation.
 *
 * @package Avltree\Bee\Connection
 */
class SocketConnection extends AbstractConnection
{
    const SOCKET_PATTERN = 'tcp://%s:%d';

    /**
     * @var resource
     */
    protected $socket;

    /**
     * @inheritdoc
     */
    public function start()
    {
        $this->logger->info('Trying to establish connection...', [
            'host' => $this->host,
            'port' => $this->port
        ]);

        $this->socket = stream_socket_client(
            sprintf(static::SOCKET_PATTERN, $this->host, $this->port),
            $errno,
            $errstr
        );

        if (false === $this->socket) {
            throw new ConnectionException(sprintf('Connection failed, reason: %s, code: %d', $errstr, $errno));
        }
    }

    /**
     * @inheritdoc
     */
    public function readData(): ?string
    {
        if (feof($this->socket)) {
            $this->socket = null;

            throw new ConnectionException('Connection closed unexpectedly');
        }

        $data = fgets($this->socket);

        if (false === $data) {
            $this->logger->warning('Failed reading data from socket');
        }

        return empty($data) ? null : $data;
    }

    /**
     * @inheritdoc
     */
    public function isConnected(): bool
    {
        return !empty($this->socket);
    }

    /**
     * @inheritdoc
     */
    protected function executeSendMessage(string $name, ?string $payload = null)
    {
        $result = fputs($this->socket, $name . (empty($payload) ? '' : ' ' . $payload) . "\n");

        if (false === $result) {
            throw new ConnectionException('Error writing to socket');
        }
    }
}
