<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Console\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Isfett\PhpAnalyzer\Builder\FinderBuilderInterface;
use Isfett\PhpAnalyzer\Builder\ProcessorBuilderInterface;
use Isfett\PhpAnalyzer\Builder\SortConfigurationBuilderInterface;
use Isfett\PhpAnalyzer\Builder\VisitorBuilderInterface;
use Isfett\PhpAnalyzer\Console\AbstractCommand;
use Isfett\PhpAnalyzer\Console\Application;
use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\ProcessorRunnerInterface;
use Isfett\PhpAnalyzer\Service\NodeRepresentationService;
use Isfett\PhpAnalyzer\Service\SortService;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\Node\Expr\UnaryMinus;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MagicNumberDetectorCommand
 */
class MagicNumberDetectorCommand extends AbstractCommand
{
    /** @var string */
    private const COMMAND_HELP = 'Find magic numbers';

    /** @var string */
    private const COMMAND_NAME = 'magic-number-detector';

    /** @var string */
    private const DEFAULT_VISITORS = 'Argument,Array,Assign,Condition,DefaultParameter,Operation,Property,Return,SwitchCase,Ternary'; // phpcs:ignore Generic.Files.LineLength.MaxExceeded

    /** @var string */
    protected const FORMAT_PROCESSORS_DONE_MESSAGE = 'Processors processed magic numbers. Magic numbers found: %d';

    /** @var string */
    protected const FORMAT_PROCESSORS_PROGRESS_MESSAGE = 'Processor %d is processing magic numbers. Magic numbers found: %d'; // phpcs:ignore Generic.Files.LineLength.MaxExceeded

    /** @var string */
    protected const FORMAT_VISITORS_DONE_MESSAGE = 'Visitors checked magic numbers in %d files. Magic numbers found: %d'; // phpcs:ignore Generic.Files.LineLength.MaxExceeded

    /** @var string */
    protected const FORMAT_VISITORS_PROGRESS_MESSAGE = 'Visitors are checking for magic numbers in files. Magic numbers found: %d'; // phpcs:ignore Generic.Files.LineLength.MaxExceeded

    /** @var string */
    private const HEADER_NUMBER = 'Number';

    /** @var string */
    private const HEADER_OCCURRENCE = 'Occurrence';

    /** @var string */
    private const PROCESSOR_PREFIX = self::VISITOR_PROCESSOR_PREFIX;

    /** @var string */
    private const VISITOR_PREFIX = self::VISITOR_PROCESSOR_PREFIX;

    /** @var string */
    private const VISITOR_PROCESSOR_PREFIX = 'MagicNumber';

    /**
     * MagicNumberDetectorCommand constructor.
     *
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
        FinderBuilderInterface $finderBuilder,
        NodeRepresentationService $nodeRepresentationService,
        ProcessorBuilderInterface $processorBuilder,
        ProcessorRunnerInterface $processorRunner,
        SortConfigurationBuilderInterface $sortConfigurationBuilder,
        SortService $sortService,
        VisitorBuilderInterface $visitorBuilder
    ) {
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

        if (!$occurrenceList->getOccurrences()->count()) {
            return Application::EXIT_CODE_SUCCESS;
        }

        $sortConfiguration = $this->sortConfigurationBuilder
            ->setMaxResults(null)
            ->setFirstResult(null)
            ->addSortField(self::SORT_FIELD_VALUE, $this->getSort($input))
            ->getSortConfiguration();

        $sortedOccurrences = $this->sortService->sortOccurrenceCollectionByNodeValues(
            $occurrenceList->getOccurrences(),
            $sortConfiguration
        );

        $this->renderResultTable($sortedOccurrences, $output);

        $output->write(\PHP_EOL);

        $this->exportJson($occurrenceList, $input);

        return Application::EXIT_CODE_FAILURE;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
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
            ->setName(self::COMMAND_NAME)
            ->setHelp(self::COMMAND_HELP)
            ->addOption(
                self::ARGUMENT_SORT,
                null,
                InputOption::VALUE_REQUIRED,
                self::DESCRIPTION_SORT,
                self::DEFAULT_SORT
            );
        $this->configureDefaultFields(self::DEFAULT_VISITORS, true);
    }

    /**
     * @param ArrayCollection $sortedOccurrences
     * @param OutputInterface $output
     *
     * @return void
     */
    private function renderResultTable(
        ArrayCollection $sortedOccurrences,
        OutputInterface $output
    ): void {
        $table = new Table($output);
        $table->setColumnWidth(self::TABLE_FIRST_COLUMN_INDEX, Application::CONSOLE_TABLE_DEFAULT_MAX_WIDTH);
        $table->setHeaders([
            self::HEADER_NUMBER,
            self::HEADER_OCCURRENCE,
        ]);

        $occurrenceCounter = self::COUNTER_START;
        /** @var Occurrence $occurrence */
        foreach ($sortedOccurrences as $occurrence) {
            $occurrenceCounter++;
            $node = $occurrence->getNode();
            $parent = $node->getAttribute(self::NODE_ATTRIBUTE_PARENT);
            $isMinus = false;

            if ($this->hasParent($parent)) {
                $isMinus = $this->hasMinusSign($parent);
                $parent = $parent->getAttribute(self::NODE_ATTRIBUTE_PARENT);
            }

            $line = $this->getLineRepresentationForOccurrence($occurrence);

            $representation = $this->nodeRepresentationService->representationForNode(
                $parent
            );
            $value = $this->getIntegerNodeValue($node, $isMinus);

            $representation = $this->leftReplace(
                (string) $value,
                sprintf(self::TABLE_MESSAGE_FOCUSED_VALUE, $value),
                $representation
            );
            $table->addRow([
                $representation,
                $this->getFileLink($occurrence, $line),
            ]);
            if ($occurrenceCounter >= $sortedOccurrences->count()) {
                continue;
            }

            $table->addRow([new TableSeparator(), new TableSeparator()]);
        }

        $table->render();
    }
}
