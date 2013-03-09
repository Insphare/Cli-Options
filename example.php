<?php

include_once 'Options.php';
include_once 'Option.php';

$option = new Cli_Options(__FILE__);
$option->addShortOption('h', Cli_Options::OPTION_TYPE_REQUIRED, 'HOST', 'The host. Example: 172.0.0.1');
$option->addShortOption('p', Cli_Options::OPTION_TYPE_OPTIONAL, 'PORT', 'The Port.', 99);
$option->addShortOption('u', Cli_Options::OPTION_TYPE_OPTIONAL, 'USER', 'The User.', 'root');
$option->addShortOption('c', Cli_Options::OPTION_TYPE_NO_VALUE, 'AUTO-CONNECT', 'Auto reconnect on losing connection.');

$option->addLongOption('debug', Cli_Options::OPTION_TYPE_NO_VALUE, 'Debug-Mode', 'Debug mode. Print output on every step.');
$option->addLongOption('log', Cli_Options::OPTION_TYPE_OPTIONAL, 'Log', 'Logging the result.', 'log.txt');

$option->parse();

// php example.php -h="172.1.2.3" -u="john" --debug --log="logfile.txt"

echo PHP_EOL;
echo('h: ' . $option->getOptionValue('h')) . PHP_EOL;
echo('p: ' . $option->getOptionValue('p')) . PHP_EOL;
echo('u: ' . $option->getOptionValue('u')) . PHP_EOL;
echo('c: ' . $option->getOptionValue('c')) . PHP_EOL;
echo('debug: ' . $option->getOptionValue('debug')) . PHP_EOL;
echo('log: ' . $option->getOptionValue('log')) . PHP_EOL;
echo PHP_EOL;
