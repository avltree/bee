<?php

namespace Avltree\Bee\Connection;

use Avltree\Bee\Exception\MessageException;

/**
 * Represents a message retrieved from the IRC server.
 *
 * @package Avltree\Bee\Connection
 */
class Message
{
    const PATTERN_PREFIX = '/^((:((([A-Za-z0-9_]+)(!~?([A-Za-z0-9]+))?(@(\S+))?)|([\w\.]+)))\s)?([A-Z]+|\d{3})\s(.*)$/';

    /**
     * @var string
     */
    protected $raw;

    /**
     * @var string|null
     */
    protected $prefix;

    /**
     * @var string|null
     */
    protected $servername;

    /**
     * @var string|null
     */
    protected $nick;

    /**
     * @var string|null
     */
    protected $user;

    /**
     * @var string|null
     */
    protected $host;

    /**
     * @var string
     */
    protected $command;

    /**
     * @var array
     */
    protected $params;

    /**
     * Message constructor.
     *
     * @param $raw
     */
    public function __construct($raw)
    {
        $this->raw = $raw;
        $this->parseMessage();
    }

    /**
     * Used to parse the raw message and fill the specific class members with proper data.
     */
    protected function parseMessage()
    {
        if (preg_match(self::PATTERN_PREFIX, $this->raw, $matches)) {
            $this->prefix = $matches[2] ?? null;
            $this->servername = $matches[10] ?? null;
            $this->nick = $matches[5] ?? null;
            $this->user = $matches[7] ?? null;
            $this->host = $matches[9] ?? null;
            $this->command = $matches[11];
            list($middle, $trailing) = array_pad(explode(' :', trim($matches[12]), 2), 2, null);
            $this->params = empty($trailing) ? [$middle] : array_merge(preg_split('/\s/', $middle), [$trailing]);
        } else {
            throw new MessageException(sprintf(
                "Message '%s' does not match the pattern, check specification",
                $this->raw
            ));
        }
    }

    /**
     * Gets the raw message.
     *
     * @return string
     */
    public function getRaw(): string
    {
        return $this->raw;
    }

    /**
     * Gets the message prefix, if present.
     *
     * @return string|null
     */
    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    /**
     * Gets the servername, if present.
     *
     * @return string|null
     */
    public function getServername(): ?string
    {
        return $this->servername;
    }

    /**
     * Gets the nick, if present.
     *
     * @return string|null
     */
    public function getNick(): ?string
    {
        return $this->nick;
    }

    /**
     * Gets the user name, if present.
     *
     * @return string|null
     */
    public function getUser(): ?string
    {
        return $this->user;
    }

    /**
     * Gets the hostname, if present.
     *
     * @return string|null
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * Gets the command name.
     *
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * Gets the message params.
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
