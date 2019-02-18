<?php

namespace Avltree\Bee\Bot\Command;

/**
 * Used to command the bot to part a specified channel.
 *
 * @package Avltree\Bee\Bot\Command
 */
class PartChannelCommand extends AbstractCommand
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'part';
    }

    /**
     * @inheritDoc
     */
    public function getMinimalLevel(): int
    {
        return 1;
    }

    /**
     * @inheritDoc
     */
    public function getUsageFlags(): int
    {
        return self::CHANNEL | self::PRIV;
    }

    /**
     * @inheritDoc
     */
    protected function doExecute(Invocation $invocation)
    {
        // TODO parameter validation

        $this->bot->leaveChannel($invocation->getParams()[1]);
    }

}