<?php

namespace Avltree\Bee\Bot;

use Avltree\Bee\Bot\Command\Invocation;
use Avltree\Bee\Connection\ConnectionInterface;
use Avltree\Bee\Connection\Messages;
use Avltree\Bee\Engine\EngineInterface;
use Avltree\Bee\Engine\EventBasedEngine;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Default bot class.
 *
 * @package Avltree\Bee\Bot
 * @todo Provide a bot interface or base class after the basic functionality is established.
 */
class Bot implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    const DEFAULT_NAME = 'Pszczoua';

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var EngineInterface
     */
    protected $engine;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $channels;

    /**
     * @var array
     */
    protected $startingChannels;

    /**
     * @var string
     */
    protected $trigger;

    /**
     * @var CommandRegistry
     */
    protected $commandRegistry;

    /**
     * Bot constructor.
     *
     * @param ConnectionInterface $connection
     * @param EngineInterface|null $engine
     * @param string $name
     * @param array $channels
     * @param LoggerInterface|null $logger
     * @param bool $registerDefaultCommands
     * @param string $trigger
     */
    public function __construct(
        ConnectionInterface $connection,
        ?EngineInterface $engine = null,
        string $name = self::DEFAULT_NAME,
        array $channels = [],
        ?LoggerInterface $logger = null,
        bool $registerDefaultCommands = true,
        string $trigger = '%'
    )
    {
        $this->connection = $connection;
        $this->engine = $engine ?? new EventBasedEngine($connection, $this, $logger);
        $this->name = $name;
        $this->startingChannels = $channels;
        $this->trigger = $trigger;
        $this->commandRegistry = new CommandRegistry($this, $logger);
        $this->logger = $logger ?? new NullLogger();

        if ($registerDefaultCommands) {
            $this->commandRegistry->registerDefaults();
        }
    }

    /**
     * Connects to the IRC server and starts working.
     */
    public function connect()
    {
        $this->engine->run();
    }

    /**
     * Gets the bot name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets the channel names on which the bot is currently present.
     *
     * @return array
     */
    public function getChannels(): array
    {
        // array_keys, because $this->channels is a flipped array
        return array_keys($this->channels);
    }

    /**
     * Gets the starting channels - the channels which the bot should join on connect.
     *
     * @return array
     */
    public function getStartingChannels(): array
    {
        return $this->startingChannels;
    }

    /**
     * Joins a channel.
     *
     * @param string $channel
     * @todo Channel name validation
     */
    public function joinChannel(string $channel)
    {
        // TODO channel name should be validated as in the bot starting command.
        if ('#' !== $channel[0]) {
            $channel = '#' . $channel;
        }

        if (isset($this->channels[$channel])) {
            // TODO inform the requesting user about the failure
            $this->logger->warning(sprintf(
                "Bot '%s' tried to join channel '%s' in which it's already present",
                $this->name,
                $channel
            ));

            return;
        }

        $this->connection->sendMessage(Messages::JOIN, $channel);
    }

    /**
     * Adds a channel to the list of already occupied channels.
     *
     * @param string $channel
     */
    public function addChannelListing(string $channel)
    {
        $this->logger->debug(sprintf("Registering JOIN to channel '%s'", $channel));

        // Indexed by channel names for efficient searching.
        $this->channels[$channel] = 1;
    }

    /**
     * Leaves a channel.
     *
     * @param string $channel
     * @todo Channel name validation
     * @todo DRY
     */
    public function leaveChannel(string $channel)
    {
        // TODO channel name should be validated as in the bot starting command.
        if ('#' !== $channel[0]) {
            $channel = '#' . $channel;
        }

        if (!isset($this->channels[$channel])) {
            // TODO inform the requesting user about the failure
            $this->logger->warning(sprintf(
                "Bot '%s' tried to leave channel '%s' in which it's not present",
                $this->name,
                $channel
            ));

            return;
        }

        $this->connection->sendMessage(Messages::PART, $channel);
    }

    /**
     * Removes a channel from the list of already occupied channels.
     *
     * @param string $channel
     */
    public function removeChannelListing(string $channel)
    {
        $this->logger->debug(sprintf("Registering PART from channel '%s'", $channel));

        unset($this->channels[$channel]);
    }

    /**
     * Gets the trigger character.
     *
     * @return string
     */
    public function getTrigger(): string
    {
        return $this->trigger;
    }

    /**
     * Handles a bot command invocation.
     *
     * @param Invocation $invocation
     */
    public function handleInvocation(Invocation $invocation)
    {
        $this->commandRegistry->runCommandIfRegistered($invocation);
    }
}
