<?php

namespace Avltree\Bee\Connection;

/**
 * Contains available IRC message names.
 *
 * @package Avltree\Bee\Connection
 */
final class Messages
{
    const ADMIN    = 'ADMIN';
    const AWAY     = 'AWAY';
    const CONNECT  = 'CONNECT';
    const ERROR    = 'ERROR';
    const INFO     = 'INFO';
    const INVITE   = 'INVITE';
    const ISON     = 'ISON';
    const JOIN     = 'JOIN';
    const KICK     = 'KICK';
    const KILL     = 'KILL';
    const LINKS    = 'LINKS';
    const LIST     = 'LIST';
    const MODE     = 'MODE';
    const NAMES    = 'NAMES';
    const NICK     = 'NICK';
    const NOTICE   = 'NOTICE';
    const OPER     = 'OPER';
    const PART     = 'PART';
    const PASS     = 'PASS';
    const PING     = 'PING';
    const PONG     = 'PONG';
    const PRIVMSG  = 'PRIVMSG';
    const QUIT     = 'QUIT';
    const REHASH   = 'REHASH';
    const RESTART  = 'RESTART';
    const SERVER   = 'SERVER';
    const SQUIT    = 'SQUIT';
    const STATS    = 'STATS';
    const SUMMON   = 'SUMMON';
    const TIME     = 'TIME';
    const TOPIC    = 'TOPIC';
    const TRACE    = 'TRACE';
    const USER     = 'USER';
    const USERHOST = 'USERHOST';
    const USERS    = 'USERS';
    const VERSION  = 'VERSION';
    const WALLOPS  = 'WALLOPS';
    const WHO      = 'WHO';
    const WHOIS    = 'WHOIS';
    const WHOWAS   = 'WHOWAS';

    /**
     * @var array
     */
    private static $nameCache;

    /**
     * Gets the names of available messages.
     *
     * @return array
     * @throws \ReflectionException
     */
    public static function getAvailableNames(): array
    {
        if (is_null(self::$nameCache)) {
            $ref = new \ReflectionClass(__CLASS__);
            self::$nameCache = array_flip($ref->getConstants());
        }

        return self::$nameCache;
    }
}
