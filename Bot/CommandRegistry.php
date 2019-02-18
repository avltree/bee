<?php

namespace Avltree\Bee\Bot;


use Avltree\Bee\Bot\Command\CommandInterface;
use Avltree\Bee\Bot\Command\Invocation;
use Avltree\Bee\Bot\Command\JoinChannelCommand;
use Avltree\Bee\Bot\Command\PartChannelCommand;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Bot command registry/
 *
 * @package Avltree\Bee\Bot
 */
class CommandRegistry implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    const CHANNEL = 'channel';
    const PRIVATE = 'private';
    /**
     * @var Bot
     */
    protected $bot;

    /**
     * @var array
     */
    protected $commands;

    /**
     * CommandRegistry constructor.
     *
     * @param Bot $bot
     * @param LoggerInterface|null $logger
     */
    public function __construct(Bot $bot, ?LoggerInterface $logger = null)
    {
        $this->bot = $bot;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * Registers the default commands.
     * @todo Register them automatically by using the Filesystem component.
     */
    public function registerDefaults()
    {
        $this->register(new JoinChannelCommand($this->bot));
        $this->register(new PartChannelCommand($this->bot));
    }

    /**
     * Registers a command in the registry.
     *
     * @param CommandInterface $command
     */
    public function register(CommandInterface $command)
    {
        $name = $command->getName();
        $flags = $command->getUsageFlags();

        $this->logger->debug(sprintf("Registering command '%s'", $name), [
            'flags' => $flags,
            'minimal_level' => $command->getMinimalLevel()
        ]);

        // TODO maybe log a warning when a command is overwritten
        if ($flags & CommandInterface::PRIV) {
            $this->commands[self::PRIVATE][$name] = $command;
        }

        if ($flags & CommandInterface::CHANNEL) {
            $this->commands[self::CHANNEL][$name] = $command;
        }
    }

    /**
     * Checks if the registry contains a command specified by the provided invocation and, if so, executes it.
     *
     * @param Invocation $invocation
     */
    public function runCommandIfRegistered(Invocation $invocation)
    {
        $commandName = $invocation->getParams()[0];
        $prefixKey = $invocation->isOnChannel() ? self::CHANNEL : self::PRIVATE;

        if ($this->bot->getTrigger() === $commandName[0]) {
            $commandName = substr($commandName, 1);
        }

        if (isset($this->commands[$prefixKey][$commandName])) {
            $this->commands[$prefixKey][$commandName]->execute($invocation);
        } else {
            $this->logger->info(sprintf("Tried to execute nonexistent command '%s'", $commandName), [
                'author' => $invocation->getAuthor(),
                'target' => $invocation->getTarget(),
                'params' => $invocation->getParams()
            ]);
        }
    }
}
