<?php

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

use Isfett\PhpAnalyzer\Kernel;
use Isfett\PhpAnalyzer\Console\Application;
use Symfony\Component\EventDispatcher\EventDispatcher;

$environment = $_SERVER['APPLICATION_ENV'] ?? 'dev';
if (false !== getenv('APPLICATION_ENV')) {
    $environment = getenv('APPLICATION_ENV');
}
$isDebug = 'dev' === $environment;

$dispatcher = new EventDispatcher();
$kernel = new Kernel($environment, $isDebug);
$kernel->boot();

$container = $kernel->getContainer();
$application = $container->get(Application::class);
$application->setDispatcher($dispatcher);
$application->run();
