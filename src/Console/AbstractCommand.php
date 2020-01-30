<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Console;

use Isfett\PhpAnalyzer\Builder\FinderBuilderInterface;
use Isfett\PhpAnalyzer\Builder\ProcessorBuilderInterface;
use Isfett\PhpAnalyzer\Builder\SortConfigurationBuilder;
use Isfett\PhpAnalyzer\Builder\SortConfigurationBuilderInterface;
use Isfett\PhpAnalyzer\Builder\VisitorBuilderInterface;
use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\DAO\OccurrenceList;
use Isfett\PhpAnalyzer\Finder\Finder;
use Isfett\PhpAnalyzer\Kernel;
use Isfett\PhpAnalyzer\Node\AbstractVisitor;
use Isfett\PhpAnalyzer\Node\ProcessorRunner;
use Isfett\PhpAnalyzer\Node\ProcessorRunnerInterface;
use Isfett\PhpAnalyzer\Node\Traverser;
use Isfett\PhpAnalyzer\Node\VisitorConnector\ParentConnector;
use Isfett\PhpAnalyzer\Service\NodeRepresentationService;
use Isfett\PhpAnalyzer\Service\SortService;
use PhpParser\Error;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class AbstractCommand
 */
abstract class AbstractCommand extends Command
{
    /** @var string */
    protected const ARGUMENT_DIRECTORY = 'directory';

    /** @var string */
    protected const ARGUMENT_EXCLUDES = 'excludes';

    /** @var string */
    protected const ARGUMENT_EXCLUDE_FILES = 'exclude-files';

    /** @var string */
    protected const ARGUMENT_EXCLUDE_PATHS = 'exclude-paths';

    /** @var string */
    protected const ARGUMENT_INCLUDE_FILES = 'include-files';

    /** @var string */
    protected const ARGUMENT_PROCESSORS = 'processors';

    /** @var string */
    protected const ARGUMENT_SORT = 'sort';

    /** @var string */
    protected const ARGUMENT_SUFFIXES = 'suffixes';

    /** @var string */
    protected const ARGUMENT_VISITORS = 'visitors';

    /** @var string */
    protected const COMMA = ',';

    /** @var string */
    protected const COMMA_WITH_SPACE = ', ';

    /** @var int */
    protected const COUNTER_START = 0;

    /** @var string */
    protected const DEFAULT_EXCLUDES = 'vendor';

    /** @var string */
    protected const DEFAULT_EXCLUDE_FILES = self::EMPTY_STRING;

    /** @var string */
    protected const DEFAULT_EXCLUDE_PATHS = self::EMPTY_STRING;

    /** @var string */
    protected const DEFAULT_INCLUDE_FILES = self::EMPTY_STRING;

    /** @var string */
    protected const DEFAULT_PROCESSORS = self::EMPTY_STRING;

    /** @var string */
    protected const DEFAULT_SORT = 'asc';

    /** @var string */
    protected const DEFAULT_SUFFIXES = 'php';

    /** @var string */
    protected const DESCRIPTION_DIRECTORY = 'path to directory which should be checked';

    /** @var string */
    protected const DESCRIPTION_EXCLUDES = 'Comma-separated string of directories-names which should be excluded (must be relative to source)'; // phpcs:ignore Generic.Files.LineLength.MaxExceeded

    /** @var string */
    protected const DESCRIPTION_EXCLUDE_FILES = 'Comma-separated string of files which should be excluded (must be relative to source)'; // phpcs:ignore Generic.Files.LineLength.MaxExceeded

    /** @var string */
    protected const DESCRIPTION_EXCLUDE_PATHS = 'Comma-separated string of directories-paths which should be excluded (must be relative to source)'; // phpcs:ignore Generic.Files.LineLength.MaxExceeded

    /** @var string */
    protected const DESCRIPTION_INCLUDE_FILES = 'Comma-separated string of files which should be included (must be relative to source)'; // phpcs:ignore Generic.Files.LineLength.MaxExceeded

    /** @var string */
    protected const DESCRIPTION_PROCESSORS = 'Comma-separated string of processors which should transform the conditions (on wrong input you can see a list of possible processor)'; // phpcs:ignore Generic.Files.LineLength.MaxExceeded

    /** @var string */
    protected const DESCRIPTION_SORT = 'sort direction of conditions, desc or asc'; // phpcs:ignore Generic.Files.LineLength.MaxExceeded

    /** @var string */
    protected const DESCRIPTION_SUFFIXES = 'Comma-separated string of valid source code filename extensions';

    /** @var string */
    protected const DESCRIPTION_VISITORS = 'Comma-separated string of visitors which should check the source code to find conditions (on wrong input you can see a list of possible visitors)'; // phpcs:ignore Generic.Files.LineLength.MaxExceeded

    /** @var string */
    protected const EMPTY_STRING = ''; // phpcs:ignore Generic.Files.LineLength.MaxExceeded

    /** @var string */
    protected const FORMAT_ADD_PROCESSOR = 'Adding %s Processor';

    /** @var string */
    private const FORMAT_ADD_VISITOR = 'Adding %s Visitor';

    /** @var string */
    protected const FORMAT_COMMAND_START_MESSAGE = '<command-start>Starting %s command</command-start>';

    /** @var string */
    private const FORMAT_FILENAME = '(%s)';

    /** @var string */
    private const FORMAT_FINDER_PROGRESS = 'Looking for files. File count: %d';

    /** @var string */
    private const FORMAT_LINK_PATH_LINK = '<href=file://%s>%s:%s</>';

    /** @var string */
    private const FORMAT_PATH_LINE = '%s:%s';

    /** @var string */
    protected const FORMAT_PROCESSORS_DONE_MESSAGE = '';

    /** @var string */
    protected const FORMAT_PROCESSORS_PROGRESS_MESSAGE = '';

    /** @var string */
    private const FORMAT_TO_LINE = '%s%s';

    /** @var string */
    protected const FORMAT_VISITORS_DONE_MESSAGE = '';

    /** @var string */
    protected const FORMAT_VISITORS_PROGRESS_MESSAGE = '';

    /** @var string */
    private const LINE_SEPARATOR = '-'; // phpcs:ignore Generic.Files.LineLength.MaxExceeded

    /** @var string */
    protected const NODE_ATTRIBUTE_PARENT = 'parent';

    /** @var string */
    protected const NO_FILES_FOUND_ERROR_MESSAGE = '<error>No files found</error>';

    /** @var string */
    protected const NO_SUCH_DIRECTORY_EXCEPTION_MESSAGE = 'Directory %s does not exist';

    /** @var int */
    private const PROGRESSBAR_DEFAULT_MAX = 0;

    /** @var string */
    private const PROGRESSBAR_MESSAGE_FINDER_START = 'Looking for files';

    /** @var string */
    private const PROGRESSBAR_NAME_FILENAME = 'filename';

    /** @var string */
    protected const SEMICOLON = ';';

    /** @var string */
    private const SEPARATOR_NAMESPACE = '\\';

    /** @var string */
    protected const SORT_FIELD_CONDITION = 'condition';

    /** @var string */
    protected const SORT_FIELD_COUNT = 'count';

    /** @var string */
    protected const SORT_FIELD_VALUE = 'value';

    /** @var int */
    private const SYMFONY_43 = 40300;

    /** @var int */
    protected const TABLE_FIRST_COLUMN_INDEX = 0;

    /** @var string */
    protected const TABLE_MESSAGE_FOCUSED_VALUE = '<focus>%s</focus>';

    /** @var FinderBuilderInterface */
    protected $finderBuilder;

    /** @var NodeRepresentationService */
    protected $nodeRepresentationService;

    /** @var ProcessorBuilderInterface */
    protected $processorBuilder;

    /** @var ProcessorRunner */
    protected $processorRunner;

    /** @var SortConfigurationBuilder */
    protected $sortConfigurationBuilder;

    /** @var SortService */
    protected $sortService;

    /** @var VisitorBuilderInterface */
    protected $visitorBuilder;

    /**
     * AbstractCommand constructor.
     *
     * @param string                            $commandName
     * @param FinderBuilderInterface            $finderBuilder
     * @param NodeRepresentationService         $nodeRepresentationService
     * @param ProcessorBuilderInterface         $processorBuilder
     * @param ProcessorRunnerInterface          $processorRunner
     * @param SortConfigurationBuilderInterface $sortConfigurationBuilder
     * @param SortService                       $sortService
     * @param VisitorBuilderInterface           $visitorBuilder
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        string $commandName,
        FinderBuilderInterface $finderBuilder,
        NodeRepresentationService $nodeRepresentationService,
        ProcessorBuilderInterface $processorBuilder,
        ProcessorRunnerInterface $processorRunner,
        SortConfigurationBuilderInterface $sortConfigurationBuilder,
        SortService $sortService,
        VisitorBuilderInterface $visitorBuilder
    ) {
        $this->finderBuilder = $finderBuilder;
        $this->nodeRepresentationService = $nodeRepresentationService;
        $this->processorBuilder = $processorBuilder;
        $this->processorRunner = $processorRunner;
        $this->sortConfigurationBuilder = $sortConfigurationBuilder;
        $this->sortService = $sortService;
        $this->visitorBuilder = $visitorBuilder;

        parent::__construct($commandName);
    }

    /**
     * @param OutputInterface $output
     * @param string          $format
     * @param int             $max
     *
     * @return ProgressBar
     */
    protected function createProgressBar(
        OutputInterface $output,
        string $format,
        int $max = self::PROGRESSBAR_DEFAULT_MAX
    ): ProgressBar {
        $progressBar = new ProgressBar($output, $max);
        $progressBar->setFormat($format);
        $progressBar->start();

        return $progressBar;
    }

    /**
     * @param ProgressBar     $progressBar
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function finishProgressBar(ProgressBar $progressBar, OutputInterface $output): void
    {
        $progressBar->setFormat(Application::PROGRESSBAR_NAME_DURATION);
        $progressBar->finish();
        $output->writeln(self::EMPTY_STRING);
    }

    /**
     * @param string $classname
     *
     * @return string
     */
    protected function getClassnameWithoutNamespace(string $classname): string
    {
        $classWithNamespaces = explode(self::SEPARATOR_NAMESPACE, $classname);

        return end($classWithNamespaces);
    }

    /**
     * @param Occurrence $occurrence
     * @param string     $line
     *
     * @return string
     */
    protected function getFileLink(Occurrence $occurrence, string $line): string
    {
        if (Kernel::VERSION_ID >= self::SYMFONY_43) { //symfony 4.3 feature
            return sprintf(
                self::FORMAT_LINK_PATH_LINK,
                $occurrence->getFile()->getPathname(),
                $occurrence->getFile()->getRelativePathname(),
                $line
            );
        }

        return sprintf(
            self::FORMAT_PATH_LINE,
            $occurrence->getFile()->getRelativePathname(),
            $line
        );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return array
     */
    protected function getFiles(InputInterface $input, OutputInterface $output): array
    {
        $directory = realpath($input->getArgument(self::ARGUMENT_DIRECTORY));
        if (false === $directory) {
            throw new \RuntimeException(sprintf(
                self::NO_SUCH_DIRECTORY_EXCEPTION_MESSAGE,
                $input->getArgument(self::ARGUMENT_DIRECTORY)
            ));
        }

        $finderProgressBar = $this->createProgressBar($output, Application::PROGRESSBAR_NAME_FINDER);

        $finder = $this->finderBuilder
            ->setDirectories([$directory])
            ->setIncludeFiles($this->getArrayOption(self::ARGUMENT_INCLUDE_FILES, $input))
            ->setExcludes($this->getArrayOption(self::ARGUMENT_EXCLUDES, $input))
            ->setExcludePaths($this->getArrayOption(self::ARGUMENT_EXCLUDE_PATHS, $input))
            ->setExcludeFiles($this->getArrayOption(self::ARGUMENT_EXCLUDE_FILES, $input))
            ->setSuffixes($this->getArrayOption(self::ARGUMENT_SUFFIXES, $input))
            ->getFinder();

        $files = $this->processFinder($finder, $finderProgressBar);

        $this->finishProgressBar($finderProgressBar, $output);

        if (!count($files)) {
            throw new \RuntimeException(self::NO_FILES_FOUND_ERROR_MESSAGE);
        }

        return $files;
    }

    /**
     * @param Occurrence $occurrence
     *
     * @return string
     */
    protected function getLineRepresentationForOccurrence(Occurrence $occurrence): string
    {
        $line = (string) $occurrence->getNode()->getStartLine();
        if ($occurrence->getNode()->getStartLine() !== $occurrence->getNode()->getEndLine()) {
            $line .= sprintf(
                self::FORMAT_TO_LINE,
                self::LINE_SEPARATOR,
                $occurrence->getNode()->getEndLine()
            );
        }

        return $line;
    }

    /**
     * @param InputInterface $input
     *
     * @return string
     */
    protected function getSort(InputInterface $input): string
    {
        return $input->getOption(self::ARGUMENT_SORT);
    }

    /**
     * @param string $search
     * @param string $replace
     * @param string $subject
     *
     * @return string
     */
    protected function leftReplace(string $search, string $replace, string $subject): string
    {
        $pos = strrpos($subject, $search);
        if (false !== $pos) {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }

    /**
     * @param array       $files
     * @param Traverser   $traverser
     * @param ProgressBar $traverserProgressBar
     *
     * @return void
     */
    protected function parseFiles(array $files, Traverser $traverser, ProgressBar $traverserProgressBar): void
    {
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);

        foreach ($files as $file) {
            try {
                $ast = $parser->parse($file->getContents());
            } catch (Error $exception) {
                continue;
            } finally {
                $traverserProgressBar->advance();
            }

            $traverser->setFile($file);
            $traverser->traverse($ast);
            $traverserProgressBar->setMessage(sprintf(
                static::FORMAT_VISITORS_PROGRESS_MESSAGE,
                $traverser->getNodeOccurrencesCount()
            ));
        }
    }

    /**
     * @param Finder      $finder
     * @param ProgressBar $progressBar
     *
     * @return array
     */
    protected function processFinder(Finder $finder, ProgressBar $progressBar): array
    {
        $files = [];
        $progressBar->setMessage(self::PROGRESSBAR_MESSAGE_FINDER_START);

        /** @var SplFileInfo $file */
        foreach ($finder->getIterator() as $file) {
            $files[] = $file;
            $progressBar->setMessage(
                sprintf(self::FORMAT_FILENAME, $file->getRelativePathname()),
                self::PROGRESSBAR_NAME_FILENAME
            );
            $progressBar->setMessage(sprintf(self::FORMAT_FINDER_PROGRESS, count($files)));
            $progressBar->advance();
        }

        return $files;
    }

    /**
     * @param OccurrenceList  $occurrenceList
     * @param string          $processorPrefix
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function processOccurrences(
        OccurrenceList $occurrenceList,
        string $processorPrefix,
        InputInterface $input,
        OutputInterface $output
    ): void {
        $processors = $this->processorBuilder
            ->setPrefix($processorPrefix)
            ->setNames($input->getOption(self::ARGUMENT_PROCESSORS))
            ->getProcessors();

        if (!count($processors)) {
            return;
        }

        foreach ($processors as $processor) {
            $output->writeln(sprintf(
                self::FORMAT_ADD_PROCESSOR,
                $this->getClassnameWithoutNamespace(get_class($processor))
            ));
            $this->processorRunner->addProcessor($processor);
        }

        $processorsProgressBar = $this->createProgressBar(
            $output,
            Application::PROGRESSBAR_NAME_CUSTOM_BAR,
            Application::CONSOLE_TABLE_DEFAULT_MAX_WIDTH
        );

        foreach ($this->processorRunner->process($occurrenceList) as $processorsDone) {
            $processorsProgressBar->setMessage(sprintf(
                static::FORMAT_PROCESSORS_PROGRESS_MESSAGE,
                $processorsDone,
                count($occurrenceList->getOccurrences())
            ));
            $processorsProgressBar->advance();
        }

        $processorsProgressBar->setMessage(sprintf(
            static::FORMAT_PROCESSORS_DONE_MESSAGE,
            count($occurrenceList->getOccurrences())
        ));
        $this->finishProgressBar($processorsProgressBar, $output);
    }

    /**
     * @param string          $commandName
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function startCommand(string $commandName, OutputInterface $output): void
    {
        $output->write([sprintf(self::FORMAT_COMMAND_START_MESSAGE, $commandName)], [self::EMPTY_STRING]);
    }

    /**
     * @param array           $files
     * @param string          $visitorPrefix
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return OccurrenceList
     */
    protected function traverseFiles(
        array $files,
        string $visitorPrefix,
        InputInterface $input,
        OutputInterface $output
    ): OccurrenceList {
        $traverser = new Traverser();
        $traverser->addVisitor(new ParentConnector());
        $visitors = $this->visitorBuilder
            ->setPrefix($visitorPrefix)
            ->setNames($input->getOption(self::ARGUMENT_VISITORS))
            ->getVisitors();

        foreach ($visitors as $visitor) {
            $output->writeln(sprintf(
                self::FORMAT_ADD_VISITOR,
                $this->getClassnameWithoutNamespace(get_class($visitor))
            ));
            $traverser->addVisitor($visitor);
        }

        $traverserProgressBar = $this->createProgressBar(
            $output,
            Application::PROGRESSBAR_NAME_CUSTOM_BAR,
            count($files)
        );

        $this->parseFiles($files, $traverser, $traverserProgressBar);

        $traverserProgressBar->setMessage(sprintf(
            static::FORMAT_VISITORS_DONE_MESSAGE,
            count($files),
            $traverser->getNodeOccurrencesCount()
        ));
        $this->finishProgressBar($traverserProgressBar, $output);

        $occurrenceList = new OccurrenceList();

        /** @var AbstractVisitor $visitor */
        foreach ($visitors as $visitor) {
            /** @var Occurrence $occurrence */
            foreach ($visitor->getNodeOccurrenceList()->getOccurrences() as $occurrence) {
                $occurrenceList->addOccurrence($occurrence);
            }
        }

        return $occurrenceList;
    }

    /**
     * @param string         $name
     * @param InputInterface $input
     *
     * @return array
     */
    private function getArrayOption(string $name, InputInterface $input): array
    {
        if (self::EMPTY_STRING === $inputOption = $input->getOption($name)) {
            return [];
        }

        return explode(self::COMMA, $inputOption);
    }
}
