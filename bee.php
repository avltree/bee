<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/vendor/autoload.php';

use Avltree\Bee\Command\StartBotCommand;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Application;

// TODO do not hardcode the version here
$console = new Application('Bee bot', '1.0.0-dev');
$logger = new Logger('bee');
// TODO specify the log level with a environment variable
$logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));

$console->add(new StartBotCommand($logger));
$console->run();
