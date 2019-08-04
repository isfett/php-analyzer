<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Application
 */
class Application extends BaseApplication
{
    /** @var string */
    private const APPLICATION_VERSION = '1.0.0';

    /** @var string */
    private const APPLICATION_NAME = 'php-analyzer';

    /** @var string */
    private const APPLICATION_AUTHOR = 'Christopher Stenke <chris@isfett.com>';

    /** @var int */
    public const EXIT_CODE_SUCCESS = 0;

    /** @var int */
    public const EXIT_CODE_FAILURE = 1;

    /**
     * Application constructor.
     */
    public function __construct()
    {
        parent::__construct(self::APPLICATION_NAME, self::APPLICATION_VERSION);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws \Throwable
     */
    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        $this->initStyles($output);
        $this->printApplicationInfoWhenNotInQuietMode($input, $output);

        if ($this->checkParameterOptionVersion($input)) {
            return self::EXIT_CODE_SUCCESS;
        }

        if (null === $input->getFirstArgument()) {
            $input = new ArrayInput(['help']);
        }

        return parent::doRun($input, $output);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    private function printApplicationInfoWhenNotInQuietMode(InputInterface $input, OutputInterface $output): void
    {
        if (false === $input->hasParameterOption('--quiet') && false === $input->hasParameterOption('-q')) {
            $output->write(
                sprintf(
                    '%s %s by %s' . PHP_EOL,
                    $this->getName(),
                    $this->getVersion(),
                    self::APPLICATION_AUTHOR
                )
            );
        }
    }

    /**
     * @param InputInterface $input
     *
     * @return bool
     */
    private function checkParameterOptionVersion(InputInterface $input): bool
    {
        return $input->hasParameterOption('--version');
    }

    /**
     * @param OutputInterface $output
     */
    private function initStyles(OutputInterface $output): void
    {
        $outputFormatter = $output->getFormatter();
        $outputFormatter->setStyle(
            'command-start',
            new OutputFormatterStyle('red', 'black', ['bold'])
        );
        $outputFormatter->setStyle(
            'focus',
            new OutputFormatterStyle('cyan', 'black', ['bold'])
        );
        $outputFormatter->setStyle(
            'flag',
            new OutputFormatterStyle('yellow', 'black')
        );

        ProgressBar::setFormatDefinition(
            'messageOnly',
            '<info>%message%</info>'
        );
        ProgressBar::setFormatDefinition(
            'messageDuration',
            '<info>%message%</info> (took %elapsed:6s%)'
        );
        ProgressBar::setFormatDefinition(
            'customFinder',
            '%elapsed:6s% | %message% -> %filename%'
        );
        ProgressBar::setFormatDefinition(
            'customBar',
            '%current%/%max% (%percent:2s%%) [%bar%] %elapsed:6s% -> %message%'
        );
    }
}
