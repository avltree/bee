<?php

namespace Avltree\Bee\Engine;

use Avltree\Bee\Bot\Bot;
use Avltree\Bee\Connection\ConnectionInterface;
use Avltree\Bee\Connection\Message;
use Avltree\Bee\Engine\EventBasedEngine\DefaultEventSubscriber;
use Avltree\Bee\Engine\EventBasedEngine\MessageEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * The default bot engine, uses the Symfony Event Dispatcher to handle the inoming messages.
 *
 * @package Avltree\Bee\Engine
 */
class EventBasedEngine extends AbstractEngine
{
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * EventBasedEngine constructor.
     *
     * @param ConnectionInterface $connection
     * @param Bot $bot
     * @param LoggerInterface|null $logger
     * @param EventDispatcherInterface|null $dispatcher
     * @param EventSubscriberInterface|null $subscriber
     */
    public function __construct(
        ConnectionInterface $connection,
        Bot $bot,
        ?LoggerInterface $logger = null,
        ?EventDispatcherInterface $dispatcher = null,
        ?EventSubscriberInterface $subscriber = null
    ) {
        parent::__construct($connection, $bot, $logger);

        $this->dispatcher = $dispatcher ?? new EventDispatcher();
        $this->dispatcher->addSubscriber($subscriber ?? new DefaultEventSubscriber($this->bot, $this->connection));
    }

    /**
     * @inheritdoc
     */
    protected function handleMessage(Message $message)
    {
        $this->dispatcher->dispatch(strtolower($message->getCommand()), new MessageEvent($message));
    }
}