<?php

namespace Avltree\Bee\Connection;

use Avltree\Bee\Exception\InvalidMessageException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Abstract class used to handle common connection operations.
 *
 * @package Avltree\Bee\Connection
 */
abstract class AbstractConnection implements ConnectionInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * AbstractConnection constructor.
     *
     * @param string $host
     * @param int $port
     * @todo Hostname validation
     */
    public function __construct(string $host, int $port, ?LoggerInterface $logger = null)
    {
        $this->host = $host;
        $this->port = $port;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @inheritdoc
     * @todo Flood protection - messages should be throttled in case there are many to send.
     * @throws \ReflectionException
     */
    public function sendMessage(string $name, ?string $payload = null)
    {
        $name = strtoupper($name);
        $messages = Messages::getAvailableNames();

        if (!isset($messages[$name])) {
            throw new InvalidMessageException(sprintf('Unrecognized message: %s', $name));
        }

        $this->executeSendMessage($name, $payload);
    }

    /**
     * Used by the correct connection implementations to actually send the message, after the additional processing is
     * completed.
     *
     * @param string $name
     * @param string|null $payload
     */
    abstract protected function executeSendMessage(string $name, ?string $payload = null);
}
