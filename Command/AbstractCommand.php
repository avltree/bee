<?php

namespace Avltree\Bee\Command;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;

/**
 * Abstract class for handling bot-related operations form the CLI.
 *
 * @package Avltree\Bee\Command
 */
abstract class AbstractCommand extends Command implements LoggerAwareInterface
{
    use LoggerAwareTrait;
}
