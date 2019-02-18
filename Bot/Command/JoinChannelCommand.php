<?php

namespace Avltree\Bee\Bot\Command;

/**
 * Used to command the bot to join a specified channel.
 *
 * @package Avltree\Bee\Bot\Command
 */
class JoinChannelCommand extends AbstractCommand
{
    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'join';
    }

    /**
     * @inheritdoc
     */
    public function getMinimalLevel(): int
    {
        return 1;
    }

    /**
     * @inheritdoc
     */
    public function getUsageFlags(): int
    {
        return self::CHANNEL | self::PRIV;
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(Invocation $invocation)
    {
        // TODO parameter validation

        $this->bot->joinChannel($invocation->getParams()[1]);
    }
}
