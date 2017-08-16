#!/usr/bin/env php
<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/Commands/ReadYamlCommand.php';


$console = new Application();


$console->addCommands(
    [
        new ReadYamlCommand()
    ]
);

$console->run($input = new ArgvInput());