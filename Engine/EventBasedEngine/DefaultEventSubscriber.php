<?php

namespace Avltree\Bee\Engine\EventBasedEngine;

use Avltree\Bee\Bot\Bot;
use Avltree\Bee\Bot\Command\Invocation;
use Avltree\Bee\Connection\ConnectionInterface;
use Avltree\Bee\Connection\Messages;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * A default all-in-one event subscriber for the event-based engine.
 *
 * @package Avltree\Bee\Engine\EventBasedEngine
 */
class DefaultEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var Bot
     */
    protected $bot;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * DefaultEventSubscriber constructor.
     *
     * @param Bot $bot
     * @param ConnectionInterface $connection
     */
    public function __construct(Bot $bot, ConnectionInterface $connection)
    {
        $this->bot = $bot;
        $this->connection = $connection;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            MessageEvent::PING => 'onPing',
            MessageEvent::ENDOFMOTD => 'onEndOfMotd',
            MessageEvent::NOMOTD => 'onEndOfMotd',
            MessageEvent::PRIVMSG => 'onPrivMsg',
            MessageEvent::JOIN => 'onJoin',
            MessageEvent::PART => 'onPart'
        ];
    }

    /**
     * Performs PONG on PING ;).
     *
     * @param MessageEvent $event
     */
    public function onPing(MessageEvent $event)
    {
        $this->connection->sendMessage(Messages::PONG, $event->getMessage()->getParams()[0]);
    }

    /**
     * Triggers the auto-join after the message of the day. Configured to run after the end of the message or the
     * message is not present.
     *
     * @param MessageEvent $event
     */
    public function onEndOfMotd(MessageEvent $event)
    {
        $channels = $this->bot->getStartingChannels();

        foreach ($channels as $channel) {
            $this->bot->joinChannel($channel);
        }
    }

    /**
     * Handles the private messages - basically handles user communication and checks for command in the messages.
     *
     * @param MessageEvent $event
     * @todo Should be used to write log files
     */
    public function onPrivMsg(MessageEvent $event)
    {
        $message = $event->getMessage();
        $nick = $message->getNick();
        $params = $message->getParams();
        $target = array_shift($params);

        // TODO a real access control
        if ('Przemek' === $nick) {
            if ($this->bot->getName() === $target || $this->bot->getTrigger() === $params[0][0]) {
                $this->bot->handleInvocation(new Invocation($nick, $target, preg_split('/\s/', $params[0])));
            }
        }
    }

    /**
     * Handles the joining of users to channels. Currently updates the bot's channel list.
     *
     * @param MessageEvent $event
     * @todo Should be used to write log files
     */
    public function onJoin(MessageEvent $event)
    {
        $message = $event->getMessage();

        if ($message->getNick() === $this->bot->getName()) {
            $this->bot->addChannelListing($message->getParams()[0]);
        }
    }

    /**
     * Handles the parting of users from channels. Currently updates the bot's channel list.
     *
     * @param MessageEvent $event
     * @todo Should be used to write log files
     */
    public function onPart(MessageEvent $event)
    {
        $message = $event->getMessage();

        if ($message->getNick() === $this->bot->getName()) {
            $this->bot->removeChannelListing($message->getParams()[0]);
        }
    }
}
