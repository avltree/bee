<?php

namespace Avltree\Bee\Bot\Command;

use Avltree\Bee\Bot\Bot;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Base class for bot commands.
 *
 * @package Avltree\Bee\Bot\Command
 */
abstract class AbstractCommand implements CommandInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var Bot
     */
    protected $bot;

    /**
     * AbstractCommand constructor.
     *
     * @param Bot $bot
     */
    public function __construct(Bot $bot, ?LoggerInterface $logger = null)
    {
        $this->bot = $bot;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @inheritdoc
     */
    public function execute(Invocation $invocation)
    {
        // TODO user level checking

        $this->logger->debug(sprintf("Executing command '%s'", $this->getName()), [
            'author' => $invocation->getAuthor(),
            'target' => $invocation->getTarget(),
            'params' => $invocation->getParams()
        ]);

        $this->doExecute($invocation);
    }

    /**
     * Performs the actual execution by a specific command.
     *
     * @param Invocation $invocation
     */
    abstract protected function doExecute(Invocation $invocation);
}