<?php

namespace Avltree\Bee\Engine\EventBasedEngine;

use Avltree\Bee\Connection\Message;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event which is dispatched at the arrival of a message from the IRC server.
 *
 * @package Avltree\Bee\Engine\EventBasedEngine
 * @todo Implement more events!
 */
class MessageEvent extends Event
{
    const ENDOFMOTD = '376';
    const JOIN      = 'join';
    const NOMOTD    = '422';
    const PART      = 'part';
    const PING      = 'ping';
    const PRIVMSG   = 'privmsg';

    /**
     * @var Message
     */
    protected $message;

    /**
     * MessageEvent constructor.
     *
     * @param Message $payload
     */
    public function __construct(Message $payload)
    {
        $this->message = $payload;
    }

    /**
     * Gets the message.
     *
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }
}
