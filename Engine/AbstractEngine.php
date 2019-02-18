<?php

namespace Avltree\Bee\Engine;

use Avltree\Bee\Bot\Bot;
use Avltree\Bee\Connection\ConnectionInterface;
use Avltree\Bee\Connection\Message;
use Avltree\Bee\Connection\Messages;
use Avltree\Bee\Exception\ConnectionException;
use Avltree\Bee\Exception\MessageException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Implementation of common engine
 *
 * @package Avltree\Bee\Engine
 */
abstract class AbstractEngine implements EngineInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var Bot
     */
    protected $bot;

    /**
     * @var bool
     */
    protected $stopped = false;

    /**
     * AbstractEngine constructor.
     *
     * @param ConnectionInterface $connection
     * @param Bot $bot
     * @param LoggerInterface|null $logger
     */
    public function __construct(ConnectionInterface $connection, Bot $bot, ?LoggerInterface $logger = null)
    {
        $this->connection = $connection;
        $this->bot = $bot;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->stopped = false;

        try {
            if (!$this->connection->isConnected()) {
                $botName = $this->bot->getName();

                $this->connection->start();
                $this->connection->sendMessage(Messages::NICK, $botName);
                $this->connection->sendMessage(
                    Messages::USER,
                    sprintf('%1$s localhost localhost :%1$s', $this->bot->getName())
                );
            }

            while (!$this->stopped) {
                try {
                    $data = $this->connection->readData();
                    $this->logger->debug($data);

                    if (is_null($data)) {
                        continue;
                    }

                    $this->handleMessage(new Message($data));
                } catch (MessageException $e) {
                    $this->logger->warning($e->getMessage());
                }
            }
        } catch (ConnectionException $e) {
            $this->logger->error(sprintf('Connection error: %s, disconnected', $e->getMessage()));
        } catch (\Exception $e) {
            $this->logger->critical(sprintf('Unexpected error: %s, disconnected', $e->getMessage()));
        }
    }

    /**
     * Used to handle a server message by a particular engine implementation.
     *
     * @param Message $message
     * @return mixed
     */
    abstract protected function handleMessage(Message $message);

    /**
     * @inheritdoc
     */
    public function stop()
    {
        $this->stopped = true;
    }
}
