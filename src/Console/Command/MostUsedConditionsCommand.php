<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Console\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Isfett\PhpAnalyzer\Builder\ConditionListBuilderInterface;
use Isfett\PhpAnalyzer\Builder\FinderBuilderInterface;
use Isfett\PhpAnalyzer\Builder\ProcessorBuilderInterface;
use Isfett\PhpAnalyzer\Builder\SortConfigurationBuilderInterface;
use Isfett\PhpAnalyzer\Builder\VisitorBuilderInterface;
use Isfett\PhpAnalyzer\Console\AbstractCommand;
use Isfett\PhpAnalyzer\Console\Application;
use Isfett\PhpAnalyzer\DAO\Condition;
use Isfett\PhpAnalyzer\DAO\ConditionList;
use Isfett\PhpAnalyzer\DAO\CountedCondition;
use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\ConditionList\Countable;
use Isfett\PhpAnalyzer\Node\ConditionList\FlipChecking;
use Isfett\PhpAnalyzer\Node\ProcessorRunnerInterface;
use Isfett\PhpAnalyzer\Service\NodeRepresentationService;
use Isfett\PhpAnalyzer\Service\SortService;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * Class MostUsedConditionsCommand
 */
class MostUsedConditionsCommand extends AbstractCommand
{
    /** @var string */
    private const ARGUMENT_CSV_DELIMITER_SEMICOLON = 'csv-delimiter-semicolon';

    /** @var string */
    private const ARGUMENT_MAX_ENTRIES = 'max-entries';

    /** @var string */
    private const ARGUMENT_MAX_OCCURRENCES = 'max-occurrences';

    /** @var string */
    private const ARGUMENT_MIN_OCCURRENCES = 'min-occurrences';

    /** @var string */
    private const ARGUMENT_WITHOUT_AFFECTED_BY_PROCESSORS = 'without-affected-by-processors';

    /** @var string */
    private const ARGUMENT_WITHOUT_FLAGS = 'without-flags';

    /** @var string */
    private const ARGUMENT_WITHOUT_OCCURRENCES = 'without-occurrences';

    /** @var string */
    private const ARGUMENT_WITH_CSV = 'with-csv';

    /** @var string */
    private const ARGUMENT_WITH_FLIP_CHECK = 'with-flip-check';

    /** @var string */
    private const COMMAND_HELP = 'Find out your most used conditions';

    /** @var string */
    private const COMMAND_NAME = 'most-used-conditions';

    /** @var string */
    private const DESCRIPTION_CSV_DELIMITER_SEMICOLON = 'change the csv delimiter to ;';

    /** @var string */
    private const DESCRIPTION_MAX_ENTRIES = 'maximum entries';

    /** @var string */
    private const DESCRIPTION_MAX_OCCURRENCES = 'maximum occurrences';

    /** @var string */
    private const DESCRIPTION_MIN_OCCURRENCES = 'minimum occurrences';

    /** @var string */
    private const DESCRIPTION_WITHOUT_AFFECTED_BY_PROCESSORS = 'hide processors who affected a condition';

    /** @var string */
    private const DESCRIPTION_WITHOUT_FLAGS = 'hide flags (like flipped)';

    /** @var string */
    private const DESCRIPTION_WITHOUT_OCCURRENCES = 'hide occurrences';

    /** @var string */
    private const DESCRIPTION_WITH_CSV = 'enable csv export to filepath';

    /** @var string */
    private const DESCRIPTION_WITH_FLIP_CHECK = 'flip checking conditions';

    /** @var int */
    private const FIRST_ARRAY_INDEX = 0;

    /** @var string */
    private const FLAG_FLIPPED = 'flipped';

    /** @var string */
    private const FORMAT_AFFECTED_BY_PROCESSOR = '(%s)';

    /** @var string */
    private const FORMAT_CSV_EXPORTED = '<info>Exported conditions with delimiter "%s" to %s</info>';

    /** @var string */
    private const FORMAT_FLAGS = '(%s) ';

    /** @var string */
    private const FORMAT_INFO_CHECK_MULTIPLE_CONDITIONS = 'Check for multiple conditions';

    /** @var string */
    private const FORMAT_INFO_FLIP_CHECK_STATUS = 'Create ConditionList (print ast nodes). Flip-Check: %s';

    /** @var string */
    private const FORMAT_OCCURRENCE = '%s <flag>%s</flag><special-info>%s</special-info>';

    /** @var string */
    private const FORMAT_TABLE_INFO_MAX_ENTRIES = '<info>Showing maximum %d conditions.</info>';

    /** @var string */
    private const FORMAT_TABLE_INFO_MIN_OCCURRENCES = '<info>Showing conditions with at least %d occurrences</info>';

    private const FORMAT_TABLE_INFO_SORT_BY = '<info>Sort Conditions by number of occurrences %s.</info>';

    /** @var string */
    private const HEADER_CONDITION = 'Condition';

    /** @var string */
    private const HEADER_COUNT = 'Count';

    /** @var string */
    private const PROCESSOR_PREFIX = self::VISITOR_PROCESSOR_PREFIX;

    /** @var string */
    private const PROGRESSBAR_FORMAT_FLIP_CHECK_PROGRESS = 'Create ConditionList (print ast nodes). Flip-Check: %s. Flipped conditions: %d'; // phpcs:ignore Generic.Files.LineLength.MaxExceeded

    /** @var string */
    private const PROGRESSBAR_FORMAT_MULTIPLE_CONDITIONS = 'Check for multiple conditions. Unique conditions: %d';

    /** @var string */
    private const SERIALIZER_FORMAT_CSV = 'csv';

    /** @var string */
    private const VISITOR_PREFIX = self::VISITOR_PROCESSOR_PREFIX;

    /** @var string */
    private const VISITOR_PROCESSOR_PREFIX = self::HEADER_CONDITION;

    /** @var ConditionListBuilderInterface */
    private $conditionListBuilder;

    /**
     * MostUsedConditionsCommand constructor.
     *
     * @param ConditionListBuilderInterface     $conditionListBuilder
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
        ConditionListBuilderInterface $conditionListBuilder,
        FinderBuilderInterface $finderBuilder,
        NodeRepresentationService $nodeRepresentationService,
        ProcessorBuilderInterface $processorBuilder,
        ProcessorRunnerInterface $processorRunner,
        SortConfigurationBuilderInterface $sortConfigurationBuilder,
        SortService $sortService,
        VisitorBuilderInterface $visitorBuilder
    ) {
        $this->conditionListBuilder = $conditionListBuilder;

        parent::__construct(
            self::COMMAND_NAME,
            $finderBuilder,
            $nodeRepresentationService,
            $processorBuilder,
            $processorRunner,
            $sortConfigurationBuilder,
            $sortService,
            $visitorBuilder
        );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function run(InputInterface $input, OutputInterface $output): int
    {
        $this->startCommand(self::COMMAND_NAME, $output);

        $files = $this->getFiles($input, $output);

        $occurrenceList = $this->traverseFiles($files, self::VISITOR_PREFIX, $input, $output);

        $this->processOccurrences($occurrenceList, self::PROCESSOR_PREFIX, $input, $output);

        $flipChecking = $input->getOption(self::ARGUMENT_WITH_FLIP_CHECK);
        $conditionListProgressBar = $this->createProgressBar(
            $output,
            Application::PROGRESSBAR_NAME_CUSTOM_BAR,
            count($occurrenceList->getOccurrences())
        );
        $conditionListProgressBar->setMessage(sprintf(
            self::FORMAT_INFO_FLIP_CHECK_STATUS,
            $this->getFlipCheckRepresentation($flipChecking)
        ));

        /** @var ConditionList|FlipChecking $conditionList */
        $conditionList = $this->conditionListBuilder
            ->setIsFlipCheckingAware($flipChecking)
            ->getConditionList();

        $flippedConditionCounter = self::COUNTER_START;
        /** @var Occurrence $occurrence */
        foreach ($occurrenceList->getOccurrences() as $occurrence) {
            $representation = $this->nodeRepresentationService->representationForNode($occurrence->getNode());
            $condition = new Condition($representation, $occurrence);
            $conditionList->addCondition($condition);

            $conditionListProgressBar->advance();
            if (!$flipChecking || $condition->getCondition() === $representation) {
                continue;
            }

            $flippedConditionCounter++;
            $conditionListProgressBar->setMessage(
                sprintf(
                    self::PROGRESSBAR_FORMAT_FLIP_CHECK_PROGRESS,
                    $this->getFlipCheckRepresentation($flipChecking),
                    $flippedConditionCounter
                )
            );
        }

        $this->finishProgressBar($conditionListProgressBar, $output);

        $rawConditions = $conditionList->getConditions();
        $countedConditionsList = new Countable();

        $countedListProgressBar = $this->createProgressBar(
            $output,
            Application::PROGRESSBAR_NAME_CUSTOM_BAR,
            count($rawConditions)
        );
        $countedListProgressBar->setMessage(self::FORMAT_INFO_CHECK_MULTIPLE_CONDITIONS);

        foreach ($rawConditions as $condition) {
            $countedConditionsList->addCondition($condition);

            $countedListProgressBar->advance();
            $countedListProgressBar->setMessage(sprintf(
                self::PROGRESSBAR_FORMAT_MULTIPLE_CONDITIONS,
                count($countedConditionsList->getCountedConditions())
            ));
        }

        $this->finishProgressBar($countedListProgressBar, $output);

        $maxEntries = $this->getMaxEntries($input);
        $sortDirection = $this->getSort($input);

        $countedConditions = $countedConditionsList->getCountedConditions();

        $calculatedFirstResult = $this->getCalculatedFirstResult($countedConditions, $maxEntries, $sortDirection);
        $calculatedMaxResults = $this->getCalculatedMaxResults($maxEntries, $sortDirection);

        $sortConfiguration = $this->sortConfigurationBuilder
            ->setMaxResults($calculatedMaxResults)
            ->setFirstResult($calculatedFirstResult)
            ->addSortField(self::SORT_FIELD_COUNT, $sortDirection)
            ->addSortField(self::SORT_FIELD_CONDITION, Criteria::ASC)
            ->getSortConfiguration();

        $sortedConditions = $this->sortService->sortArrayCollection($countedConditions, $sortConfiguration);

        $this->renderResultTable($sortedConditions, $countedConditions, $input, $output);

        $this->exportCsv($sortedConditions, $output, $input);

        return Application::EXIT_CODE_SUCCESS;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setHelp(self::COMMAND_HELP)
            ->setDefinition(
                new InputDefinition([
                    new InputArgument(
                        self::ARGUMENT_DIRECTORY,
                        InputArgument::OPTIONAL,
                        self::DESCRIPTION_DIRECTORY,
                        getcwd()
                    ),
                ])
            )
            ->addOption(
                self::ARGUMENT_EXCLUDES,
                null,
                InputOption::VALUE_REQUIRED,
                self::DESCRIPTION_EXCLUDES,
                self::DEFAULT_EXCLUDES
            )
            ->addOption(
                self::ARGUMENT_EXCLUDE_PATHS,
                null,
                InputOption::VALUE_REQUIRED,
                self::DESCRIPTION_EXCLUDE_PATHS,
                self::DEFAULT_EXCLUDE_PATHS
            )
            ->addOption(
                self::ARGUMENT_EXCLUDE_FILES,
                null,
                InputOption::VALUE_REQUIRED,
                self::DESCRIPTION_EXCLUDE_FILES,
                self::DEFAULT_EXCLUDE_FILES
            )
            ->addOption(
                self::ARGUMENT_INCLUDE_FILES,
                null,
                InputOption::VALUE_REQUIRED,
                self::DESCRIPTION_INCLUDE_FILES,
                self::DEFAULT_INCLUDE_FILES
            )
            ->addOption(
                self::ARGUMENT_SUFFIXES,
                null,
                InputOption::VALUE_REQUIRED,
                self::DESCRIPTION_SUFFIXES,
                self::DEFAULT_SUFFIXES
            )
            ->addOption(
                self::ARGUMENT_VISITORS,
                null,
                InputOption::VALUE_REQUIRED,
                self::DESCRIPTION_VISITORS,
                self::DEFAULT_VISITORS
            )
            ->addOption(
                self::ARGUMENT_PROCESSORS,
                null,
                InputOption::VALUE_REQUIRED,
                self::DESCRIPTION_PROCESSORS,
                self::DEFAULT_PROCESSORS
            )
            ->addOption(
                self::ARGUMENT_SORT,
                null,
                InputOption::VALUE_REQUIRED,
                self::DESCRIPTION_SORT,
                self::DEFAULT_SORT
            )
            ->addOption(
                self::ARGUMENT_MAX_ENTRIES,
                null,
                InputOption::VALUE_REQUIRED,
                self::DESCRIPTION_MAX_ENTRIES
            )
            ->addOption(
                self::ARGUMENT_MAX_OCCURRENCES,
                null,
                InputOption::VALUE_REQUIRED,
                self::DESCRIPTION_MAX_OCCURRENCES
            )
            ->addOption(
                self::ARGUMENT_MIN_OCCURRENCES,
                null,
                InputOption::VALUE_REQUIRED,
                self::DESCRIPTION_MIN_OCCURRENCES
            )
            ->addOption(
                self::ARGUMENT_WITH_FLIP_CHECK,
                null,
                InputOption::VALUE_NONE,
                self::DESCRIPTION_WITH_FLIP_CHECK
            )
            ->addOption(
                self::ARGUMENT_WITHOUT_OCCURRENCES,
                null,
                InputOption::VALUE_NONE,
                self::DESCRIPTION_WITHOUT_OCCURRENCES
            )
            ->addOption(
                self::ARGUMENT_WITHOUT_FLAGS,
                null,
                InputOption::VALUE_NONE,
                self::DESCRIPTION_WITHOUT_FLAGS
            )
            ->addOption(
                self::ARGUMENT_WITHOUT_AFFECTED_BY_PROCESSORS,
                null,
                InputOption::VALUE_NONE,
                self::DESCRIPTION_WITHOUT_AFFECTED_BY_PROCESSORS
            )
            ->addOption(
                self::ARGUMENT_WITH_CSV,
                null,
                InputOption::VALUE_REQUIRED,
                self::DESCRIPTION_WITH_CSV,
                null
            )
            ->addOption(
                self::ARGUMENT_CSV_DELIMITER_SEMICOLON,
                null,
                InputOption::VALUE_NONE,
                self::DESCRIPTION_CSV_DELIMITER_SEMICOLON
            );
    }

    /**
     * @param Table            $table
     * @param CountedCondition $countedCondition
     * @param InputInterface   $input
     *
     * @return void
     */
    private function addOccurrencesToResultTable(
        Table $table,
        CountedCondition $countedCondition,
        InputInterface $input
    ): void {
        $hideOccurrences = $input->getOption(self::ARGUMENT_WITHOUT_OCCURRENCES);
        $hideFlags = $input->getOption(self::ARGUMENT_WITHOUT_FLAGS);
        $hideAffectedByProcessors = $input->getOption(self::ARGUMENT_WITHOUT_AFFECTED_BY_PROCESSORS);
        $maximumOccurrences = $this->getMaxOccurrences($input);

        if ($hideOccurrences) {
            return;
        }

        /** @var Occurrence $occurrence */
        $occurrenceCounter = self::COUNTER_START;
        $occurrenceCounter++;
        foreach ($countedCondition->getOccurrences() as $occurrence) {
            if (null !== $maximumOccurrences && $occurrenceCounter > $maximumOccurrences) {
                break;
            }

            $line = $this->getLineRepresentationForOccurrence($occurrence);

            $flags = $this->getFlags($occurrence, $hideFlags);
            $affectedByProcessors = $this->getAffectedByProcessors($occurrence, $hideAffectedByProcessors);
            $table->addRow([
                sprintf(
                    self::FORMAT_OCCURRENCE,
                    $this->getFileLink($occurrence, $line),
                    $flags,
                    $affectedByProcessors
                ),
                self::EMPTY_STRING,
            ]);
            $occurrenceCounter++;
        }
    }

    /**
     * @param ArrayCollection $sortedConditions
     * @param OutputInterface $output
     * @param InputInterface  $input
     *
     * @return void
     */
    private function exportCsv(ArrayCollection $sortedConditions, OutputInterface $output, InputInterface $input): void
    {
        $csvExport = $input->getOption(self::ARGUMENT_WITH_CSV);
        if (!$csvExport) {
            return;
        }

        $csvExportData = [];

        /** @var CountedCondition $countedCondition */
        foreach ($sortedConditions as $countedCondition) {
            $csvExportData[] = [$countedCondition->getCondition(), $countedCondition->getCount()];
        }

        $serializer = new Serializer([], [new CsvEncoder()]);
        $csvDelimiter = $input->getOption(self::ARGUMENT_CSV_DELIMITER_SEMICOLON) ? ';' : ',';
        $csvExportOptions = [
            CsvEncoder::NO_HEADERS_KEY => true,
            CsvEncoder::DELIMITER_KEY => $csvDelimiter,
        ];
        $csvDelimiterReplace = self::COMMA === $csvDelimiter ? '[comma]' : '[semicolon]';
        array_walk(
            $csvExportData,
            static function (&$data) use ($csvDelimiter, $csvDelimiterReplace) {
                $data[self::FIRST_ARRAY_INDEX] = str_replace(
                    $csvDelimiter,
                    $csvDelimiterReplace,
                    $data[self::FIRST_ARRAY_INDEX]
                );

                return $data;
            }
        );
        file_put_contents(
            $csvExport,
            $serializer->encode($csvExportData, self::SERIALIZER_FORMAT_CSV, $csvExportOptions)
        );
        $output->writeln(
            sprintf(
                self::FORMAT_CSV_EXPORTED,
                $csvDelimiter,
                realpath($csvExport)
            ) . \PHP_EOL
        );
    }

    /**
     * @param Occurrence $occurrence
     * @param bool       $hideAffectedByProcessors
     *
     * @return string
     */
    private function getAffectedByProcessors(Occurrence $occurrence, bool $hideAffectedByProcessors): string
    {
        $showAffectedByProcessors = $this->getShowAffectedByProcessors(
            $hideAffectedByProcessors,
            $occurrence->getAffectedByProcessors()
        );

        $affectedByProcessors = self::EMPTY_STRING;

        if ($showAffectedByProcessors) {
            $affectedByProcessors = sprintf(
                self::FORMAT_AFFECTED_BY_PROCESSOR,
                implode(self::COMMA_WITH_SPACE, $occurrence->getAffectedByProcessors())
            );
        }

        return $affectedByProcessors;
    }

    /**
     * @param ArrayCollection $countedConditionList
     * @param int|null        $maxEntries
     * @param string          $sortDirection
     *
     * @return int|null
     */
    private function getCalculatedFirstResult(
        ArrayCollection $countedConditionList,
        ?int $maxEntries,
        string $sortDirection
    ): ?int {
        $calculatedFirstResult = null;
        if (null !== $maxEntries && strtolower(Criteria::DESC) !== strtolower($sortDirection)) {
            $calculatedFirstResult = $countedConditionList->count() - $maxEntries;
        }

        return $calculatedFirstResult;
    }

    /**
     * @param int|null $maxEntries
     * @param string   $sortDirection
     *
     * @return int|null
     */
    private function getCalculatedMaxResults(?int $maxEntries, string $sortDirection): ?int
    {
        $calculatedMaxResults = null;
        if (null !== $maxEntries && strtolower(Criteria::DESC) === strtolower($sortDirection)) {
            $calculatedMaxResults = $maxEntries;
        }

        return $calculatedMaxResults;
    }

    /**
     * @param Occurrence $occurrence
     * @param bool       $hideFlags
     *
     * @return string
     */
    private function getFlags(Occurrence $occurrence, bool $hideFlags): string
    {
        $occurrenceFlags = $this->getOccurrenceFlags($occurrence);
        $showFlags = $this->getShowFlags($hideFlags, $occurrenceFlags);

        $flags = self::EMPTY_STRING;
        if ($showFlags) {
            $flags = sprintf(self::FORMAT_FLAGS, implode(self::COMMA_WITH_SPACE, $occurrenceFlags));
        }

        return $flags;
    }

    /**
     * @param bool $flipChecking
     *
     * @return string
     */
    private function getFlipCheckRepresentation(bool $flipChecking): string
    {
        return $flipChecking ? 'active' : 'inactive';
    }

    /**
     * @param InputInterface $input
     *
     * @return int|null
     */
    private function getMaxEntries(InputInterface $input): ?int
    {
        $maximumEntries = $input->getOption(self::ARGUMENT_MAX_ENTRIES);
        if (null !== $maximumEntries) {
            $maximumEntries = (int) $maximumEntries;
        }

        return $maximumEntries;
    }

    /**
     * @param InputInterface $input
     *
     * @return int|null
     */
    private function getMaxOccurrences(InputInterface $input): ?int
    {
        $maximumOccurrences = $input->getOption(self::ARGUMENT_MAX_OCCURRENCES);
        if (null !== $maximumOccurrences) {
            $maximumOccurrences = (int) $maximumOccurrences;
        }

        return $maximumOccurrences;
    }

    /**
     * @param InputInterface $input
     *
     * @return int|null
     */
    private function getMinOccurrences(InputInterface $input): ?int
    {
        $minOccurrences = $input->getOption(self::ARGUMENT_MIN_OCCURRENCES);
        if (null !== $minOccurrences) {
            $minOccurrences = (int) $minOccurrences;
        }

        return $minOccurrences;
    }

    /**
     * @param Occurrence $occurrence
     *
     * @return array
     */
    private function getOccurrenceFlags(Occurrence $occurrence): array
    {
        $flags = [];
        if ($occurrence->isFlipped()) {
            $flags[] = self::FLAG_FLIPPED;
        }

        return $flags;
    }

    /**
     * @param bool  $hideAffectedByProcessors
     * @param array $affectedByProcessors
     *
     * @return bool
     */
    private function getShowAffectedByProcessors(bool $hideAffectedByProcessors, array $affectedByProcessors): bool
    {
        return !$hideAffectedByProcessors && count($affectedByProcessors);
    }

    /**
     * @param bool  $hideFlags
     * @param array $flags
     *
     * @return bool
     */
    private function getShowFlags(bool $hideFlags, array $flags): bool
    {
        return !$hideFlags && count($flags);
    }

    /**
     * @param ArrayCollection $countedConditionList
     * @param int             $conditionCounter
     * @param int|null        $maxEntries
     *
     * @return bool
     */
    private function isEndOfResultTable(
        ArrayCollection $countedConditionList,
        int $conditionCounter,
        ?int $maxEntries
    ): bool {
        return (null !== $maxEntries && $conditionCounter >= $maxEntries) ||
            (null === $maxEntries && $conditionCounter >= $countedConditionList->count());
    }

    /**
     * @param ArrayCollection $sortedConditions
     * @param ArrayCollection $countedConditionList
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    private function renderResultTable(
        ArrayCollection $sortedConditions,
        ArrayCollection $countedConditionList,
        InputInterface $input,
        OutputInterface $output
    ): void {
        $maxEntries = $this->getMaxEntries($input);

        $minOccurrences = $this->getMinOccurrences($input);

        $this->showResultTableInfos($minOccurrences, $maxEntries, $input, $output);

        $table = new Table($output);
        $table->setColumnWidth(self::TABLE_FIRST_COLUMN_INDEX, Application::CONSOLE_TABLE_DEFAULT_MAX_WIDTH);
        $table->setHeaders([
            self::HEADER_CONDITION,
            self::HEADER_COUNT,
        ]);

        $conditionCounter = self::COUNTER_START;
        /** @var CountedCondition $countedCondition */
        foreach ($sortedConditions as $countedCondition) {
            $conditionCounter++;
            if (null !== $minOccurrences && $minOccurrences > $countedCondition->getCount()) {
                continue;
            }

            $table->addRow([
                sprintf(self::TABLE_MESSAGE_FOCUSED_VALUE, $countedCondition->getCondition()),
                $countedCondition->getCount(),
            ]);

            $this->addOccurrencesToResultTable($table, $countedCondition, $input);

            $isEndOfTable = $this->isEndOfResultTable($countedConditionList, $conditionCounter, $maxEntries);
            if ($isEndOfTable) {
                continue;
            }

            $table->addRow([new TableSeparator(), new TableSeparator()]);
        }

        $table->render();

        $output->write(\PHP_EOL);
    }

    /**
     * @param int|null        $minOccurrences
     * @param int|null        $maxEntries
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    private function showResultTableInfos(
        ?int $minOccurrences,
        ?int $maxEntries,
        InputInterface $input,
        OutputInterface $output
    ): void {
        $output->writeln(sprintf(
            self::FORMAT_TABLE_INFO_SORT_BY,
            $this->getSort($input)
        ));

        if (null !== $maxEntries) {
            $output->writeln(sprintf(
                self::FORMAT_TABLE_INFO_MAX_ENTRIES,
                $maxEntries
            ));
        }

        // phpcs:ignore SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
        if (null !== $minOccurrences) {
            $output->writeln(sprintf(
                self::FORMAT_TABLE_INFO_MIN_OCCURRENCES,
                $minOccurrences
            ));
        }
    }
}
