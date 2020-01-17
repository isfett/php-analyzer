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
    private const APPLICATION_AUTHOR = 'Christopher Stenke <chris@isfett.com>';

    /** @var string */
    private const APPLICATION_NAME = 'php-analyzer';

    /** @var string */
    private const APPLICATION_VERSION = '1.2.2';

    /** @var string */
    private const ARGUMENT_HELP = 'help';

    /** @var string */
    private const COLOR_BLACK = 'black';

    /** @var string */
    private const COLOR_CYAN = 'cyan';

    /** @var string */
    private const COLOR_MAGENTA = 'magenta';

    /** @var string */
    private const COLOR_RED = 'red';

    /** @var string */
    private const COLOR_YELLOW = 'yellow';

    /** @var int */
    public const CONSOLE_TABLE_DEFAULT_MAX_WIDTH = 60;

    /** @var int */
    public const EXIT_CODE_FAILURE = 1;

    /** @var int */
    public const EXIT_CODE_SUCCESS = 0;

    /** @var string */
    private const FORMAT_APPLICATION_INFO = '%s %s by %s%s';

    /** @var string */
    private const FORMAT_VERSION = '--version';

    /** @var string */
    private const PARAMETER_QUIET = '--quiet';

    /** @var string */
    private const PARAMETER_QUIET_SHORT = '-q';

    /** @var string */
    private const PROGRESSBAR_FORMAT_CUSTOM_BAR = '%current%/%max% (%percent:2s%%) [%bar%] %elapsed:6s% -> %message%';

    /** @var string */
    private const PROGRESSBAR_FORMAT_DURATION = self::PROGRESSBAR_FORMAT_MESSAGE_ONLY .
                  self::PROGRESSBAR_FORMAT_ELAPSED_TIME;

    /** @var string */
    private const PROGRESSBAR_FORMAT_ELAPSED_TIME = ' (took %elapsed:6s%)';

    /** @var string */
    private const PROGRESSBAR_FORMAT_FINDER = '%elapsed:6s% | %message% -> %filename%';

    /** @var string */
    private const PROGRESSBAR_FORMAT_MESSAGE_ONLY = '<info>%message%</info>';

    /** @var string */
    public const PROGRESSBAR_NAME_CUSTOM_BAR = 'customBar';

    /** @var string */
    public const PROGRESSBAR_NAME_DURATION = 'messageDuration';

    /** @var string */
    public const PROGRESSBAR_NAME_FINDER = 'customFinder';

    /** @var string */
    public const PROGRESSBAR_NAME_MESSAGE_ONLY = 'messageOnly';

    /** @var string */
    private const STYLE_BOLD = 'bold';

    /** @var string */
    private const STYLE_COMMAND_START = 'command-start';

    /** @var string */
    private const STYLE_FLAG = 'flag';

    /** @var string */
    private const STYLE_FOCUS = 'focus';

    /** @var string */
    private const STYLE_SPECIAL_INFO = 'special-info';

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
            $input = new ArrayInput([self::ARGUMENT_HELP]);
        }

        return parent::doRun($input, $output);
    }

    /**
     * @param InputInterface $input
     *
     * @return bool
     */
    private function checkParameterOptionVersion(InputInterface $input): bool
    {
        return $input->hasParameterOption(self::FORMAT_VERSION);
    }

    /**
     * @param OutputInterface $output
     */
    private function initStyles(OutputInterface $output): void
    {
        $outputFormatter = $output->getFormatter();
        $outputFormatter->setStyle(
            self::STYLE_COMMAND_START,
            new OutputFormatterStyle(self::COLOR_RED, self::COLOR_BLACK, [self::STYLE_BOLD])
        );
        $outputFormatter->setStyle(
            self::STYLE_FOCUS,
            new OutputFormatterStyle(self::COLOR_CYAN, self::COLOR_BLACK, [self::STYLE_BOLD])
        );
        $outputFormatter->setStyle(
            self::STYLE_FLAG,
            new OutputFormatterStyle(self::COLOR_YELLOW, self::COLOR_BLACK, [self::STYLE_BOLD])
        );
        $outputFormatter->setStyle(
            self::STYLE_SPECIAL_INFO,
            new OutputFormatterStyle(self::COLOR_MAGENTA, self::COLOR_BLACK)
        );

        ProgressBar::setFormatDefinition(
            self::PROGRESSBAR_NAME_MESSAGE_ONLY,
            self::PROGRESSBAR_FORMAT_MESSAGE_ONLY
        );
        ProgressBar::setFormatDefinition(
            self::PROGRESSBAR_NAME_DURATION,
            self::PROGRESSBAR_FORMAT_DURATION
        );
        ProgressBar::setFormatDefinition(
            self::PROGRESSBAR_NAME_FINDER,
            self::PROGRESSBAR_FORMAT_FINDER
        );
        ProgressBar::setFormatDefinition(
            self::PROGRESSBAR_NAME_CUSTOM_BAR,
            self::PROGRESSBAR_FORMAT_CUSTOM_BAR
        );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    private function printApplicationInfoWhenNotInQuietMode(InputInterface $input, OutputInterface $output): void
    {
        if (true === $input->hasParameterOption(self::PARAMETER_QUIET) ||
            true === $input->hasParameterOption(self::PARAMETER_QUIET_SHORT)
        ) {
            return;
        }

        $output->write(
            sprintf(
                self::FORMAT_APPLICATION_INFO,
                $this->getName(),
                $this->getVersion(),
                self::APPLICATION_AUTHOR,
                \PHP_EOL
            )
        );
    }
}
