#!/usr/bin/env php
<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/Commands/UpdateYaml.php';


$console = new Application();


$console->addCommands(
    [
        new UpdateYaml()
    ]
);

$console->run($input = new ArgvInput());