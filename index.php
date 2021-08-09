#!/usr/bin/env php
<?php
// application.php

require __DIR__.'/vendor/autoload.php';

use App\Ui\Command\InitializeVendingMachine;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new InitializeVendingMachine());
$application->run();
