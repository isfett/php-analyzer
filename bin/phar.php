#!/usr/bin/env php
<?php

require 'vendor/autoload.php';
use Symfony\Component\Finder\Finder;
use Isfett\PhpAnalyzer\Kernel;

// The php.ini setting phar.readonly must be set to 0
$pharFilename = 'php-analyzer.phar';

// clean up
if (file_exists($pharFilename)) {
    unlink($pharFilename);
}

// Make vendor with no dev requirements
shell_exec('composer update --no-dev --quiet');

// delete cache+logs
shell_exec('rm -R var');

// Build container
$kernel = new Kernel('prod', false);
$kernel->boot();

$finder = (new Finder())
    ->files()
    ->in(dirname(__DIR__))
    ->notName('phar.php')
    ->exclude(['codecoverage', 'docs', 'tests']);

// create phar
$phar = new Phar($pharFilename);

// creating our library using whole directory
$phar->buildFromIterator($finder->getIterator(), dirname(__DIR__));

// pointing main file which requires all classes
$phar->setStub(
"#!/usr/bin/env php
<?php
putenv('APPLICATION_ENV=prod');
Phar::mapPhar('$pharFilename');
require 'phar://$pharFilename/bin/php-analyzer.php';
__halt_compiler();"
);

// reset vendor with dev requirements
shell_exec('composer update --quiet');

echo sprintf('%s successfully created', $pharFilename) .  PHP_EOL;
