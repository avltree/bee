<?php

namespace Avltree\Bee\Bot\Command;

/**
 * Interface common for bot commands.
 *
 * @package Avltree\Bee\Bot\Command
 */
interface CommandInterface
{
    const CHANNEL = 1;
    const PRIV    = 2;

    /**
     * Gets the command's name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Gets the minimal user level to be used.
     *
     * @return int
     */
    public function getMinimalLevel(): int;

    /**
     * Executes the command.
     *
     * @param Invocation $invocation
     */
    public function execute(Invocation $invocation);

    /**
     * Returns the flags specifying if the command is to be used by a private query or called from a channel (or both).
     *
     * @return int
     */
    public function getUsageFlags(): int;
}