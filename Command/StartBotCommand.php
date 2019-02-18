<?php

namespace Avltree\Bee\Command;

use Avltree\Bee\Bot\Bot;
use Avltree\Bee\Connection\SocketConnection;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command used to start a new bot and perform some basic startup operations.
 *
 * @todo Use the Process component to be able to spawn and despawn multiple bots and demonize them
 * @package Avltree\Bee\Command
 */
class StartBotCommand extends AbstractCommand
{
    const ARGUMENT_HOSTNAME     = 'hostname';
    const OPTION_CHANNELS       = 'channels';
    const OPTION_CHANNELS_SHORT = 'c';
    const OPTION_NICK           = 'nick';
    const OPTION_NICK_SHORT     = 'N';
    const OPTION_PORT           = 'port';
    const OPTION_PORT_SHORT     = 'p';
    const PATTERN_CHANNEL_NAME  = '/#[^\x07\x2C\s]{1,200}/';
    const PATTERN_NICK          = '/[A-Za-z0-9_]+/';

    /**
     * StartBotCommand constructor.
     *
     * @param LoggerInterface|null $logger
     */
    public function __construct(?LoggerInterface $logger = null)
    {
        parent::__construct();

        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('bot:start')
            ->setDescription('Starts a single bot in the foreground')
            ->addArgument(
                self::ARGUMENT_HOSTNAME,
                InputArgument::REQUIRED,
                'The hostname of the IRC server to connect to'
            )
            ->addOption(
                self::OPTION_NICK,
                self::OPTION_NICK_SHORT,
                InputOption::VALUE_REQUIRED,
                'Nick for the bot',
                Bot::DEFAULT_NAME
            )
            ->addOption(
                self::OPTION_CHANNELS,
                self::OPTION_CHANNELS_SHORT,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Channels to join on startup'
            )
            ->addOption(
                self::OPTION_PORT,
                self::OPTION_PORT_SHORT,
                InputOption::VALUE_REQUIRED,
                'Port on which the bot will connect to the server',
                6667
            );
    }

    /**
     * @inheritDoc
     * @todo Allow using a custom trigger character.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $channels = array_map(function (string $channel) {
            if ('#' !== $channel[0]) {
                $channel = '#' . $channel;
            }

            if (!preg_match(self::PATTERN_CHANNEL_NAME, $channel)) {
                throw new \InvalidArgumentException(sprintf('Invalid channel name: %s', $channel));
            }

            return $channel;
        }, $input->getOption(self::OPTION_CHANNELS));
        $port = $input->getOption(self::OPTION_PORT);
        $nick = $input->getOption(self::OPTION_NICK);
        $hostname = $input->getArgument(self::ARGUMENT_HOSTNAME);

        if (!ctype_digit($port)) {
            throw new \InvalidArgumentException(sprintf("Invalid port '%s', use an integer", $port));
        }

        if (!preg_match(self::PATTERN_NICK, $nick)) {
            throw new \InvalidArgumentException(sprintf('Invalid nickname provided: %s', $nick));
        }

        $connection = new SocketConnection($hostname, $port, $this->logger);

        // TODO allow using different engines
        $this->logger->info(sprintf("Starting bot '%s'", $nick), [
            'hostname' => $hostname,
            'port' => $port,
            'channels' => $channels
        ]);

        $bot = new Bot($connection, null, $nick, $channels, $this->logger);
        $bot->setLogger($this->logger);
        $bot->connect();
    }
}
