<?php

namespace Avltree\Bee\Bot\Command;

/**
 * Invocation is a message send to the bot to direct it to perform a certain command.
 *
 * @package Avltree\Bee\Bot\Command
 */
class Invocation
{
    /**
     * @var string
     */
    protected $author;

    /**
     * @var string
     */
    protected $target;

    /**
     * @var bool
     */
    protected $onChannel;

    /**
     * @var array
     */
    protected $params;

    /**
     * Invocation constructor.
     *
     * @param string $author
     * @param string $target
     * @param array $params
     */
    public function __construct(string $author, string $target, array $params)
    {
        $this->author = $author;
        $this->target = $target;
        $this->params = $params;
        $this->onChannel = '#' === $target[0];
    }

    /**
     * Returns the invocation's author.
     *
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * Gets the invocation's target - could be a channel or a specific bot name.
     *
     * @return string
     */
    public function getTarget(): string
    {
        return $this->target;
    }

    /**
     * Returns true if the invocation's target is a channel, false otherwise.
     *
     * @return bool
     */
    public function isOnChannel(): bool
    {
        return $this->onChannel;
    }

    /**
     * Gets the invocation params.
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
