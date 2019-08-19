<?php

$environment = $_SERVER['APPLICATION_ENV'] ?? 'dev';
if (false !== getenv('APPLICATION_ENV')) {
    $environment = getenv('APPLICATION_ENV');
}

if (file_exists(__DIR__ . '/../../../autoload.php')) {
    // php-analyzer is part of a composer installation
    require_once __DIR__ . '/../../../autoload.php';
    $environment = 'prod';
} else {
    require_once __DIR__ . '/../vendor/autoload.php';
}

use Isfett\PhpAnalyzer\Kernel;
use Isfett\PhpAnalyzer\Console\Application;
use Symfony\Component\EventDispatcher\EventDispatcher;

$isDebug = 'dev' === $environment;

$dispatcher = new EventDispatcher();
$kernel = new Kernel($environment, $isDebug);
$kernel->boot();

$container = $kernel->getContainer();
$application = $container->get(Application::class);
$application->setDispatcher($dispatcher);
$application->run();
