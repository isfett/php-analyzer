<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Console\Command;

use Isfett\PhpAnalyzer\Builder\ConditionListBuilderInterface;
use Isfett\PhpAnalyzer\Builder\FinderBuilderInterface;
use Isfett\PhpAnalyzer\Builder\ProcessorBuilderInterface;
use Isfett\PhpAnalyzer\Builder\SortConfigurationBuilder;
use Isfett\PhpAnalyzer\Builder\SortConfigurationBuilderInterface;
use Isfett\PhpAnalyzer\Builder\VisitorBuilderInterface;
use Isfett\PhpAnalyzer\Console\Application;
use Isfett\PhpAnalyzer\DAO\Condition;
use Isfett\PhpAnalyzer\DAO\ConditionList;
use Isfett\PhpAnalyzer\DAO\CountedCondition;
use Isfett\PhpAnalyzer\Node\ConditionList\Countable;
use Isfett\PhpAnalyzer\Node\ConditionList\FlipChecking;
use Isfett\PhpAnalyzer\DAO\OccurrenceList;
use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Finder\Finder;
use Isfett\PhpAnalyzer\Node\AbstractVisitor;
use Isfett\PhpAnalyzer\Node\ProcessorRunner;
use Isfett\PhpAnalyzer\Node\ProcessorRunnerInterface;
use Isfett\PhpAnalyzer\Node\Traverser;
use Isfett\PhpAnalyzer\Service\NodeRepresentationService;
use Isfett\PhpAnalyzer\Service\SortService;
use PhpParser\Error;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class MostUsedConditionsCommand
 */
class MostUsedConditionsCommand extends Command
{
    /** @var string */
    private const NAME = 'most-used-conditions';

    /** @var FinderBuilderInterface */
    private $finderBuilder;

    /** @var ConditionListBuilderInterface */
    private $conditionListBuilder;

    /** @var VisitorBuilderInterface */
    private $visitorBuilder;

    /** @var ProcessorBuilderInterface */
    private $processorBuilder;

    /** @var SortConfigurationBuilder */
    private $sortConfigurationBuilder;

    /** @var NodeRepresentationService */
    private $nodeRepresentationService;

    /** @var SortService */
    private $sortService;

    /** @var ProcessorRunner */
    private $processorRunner;

    /**
     * MostUsedConditionsCommand constructor.
     *
     * @param FinderBuilderInterface            $finderBuilder
     * @param ConditionListBuilderInterface     $conditionListBuilder
     * @param VisitorBuilderInterface           $visitorBuilder
     * @param ProcessorBuilderInterface         $processorBuilder
     * @param SortConfigurationBuilderInterface $sortConfigurationBuilder
     * @param ProcessorRunnerInterface          $processorRunner
     * @param NodeRepresentationService         $nodeRepresentationService
     * @param SortService                       $sortService
     */
    public function __construct(
        FinderBuilderInterface $finderBuilder,
        ConditionListBuilderInterface $conditionListBuilder,
        VisitorBuilderInterface $visitorBuilder,
        ProcessorBuilderInterface $processorBuilder,
        SortConfigurationBuilderInterface $sortConfigurationBuilder,
        ProcessorRunnerInterface $processorRunner,
        NodeRepresentationService $nodeRepresentationService,
        SortService $sortService
    ) {
        $this->finderBuilder = $finderBuilder;
        $this->conditionListBuilder = $conditionListBuilder;
        $this->visitorBuilder = $visitorBuilder;
        $this->sortConfigurationBuilder = $sortConfigurationBuilder;
        $this->processorBuilder = $processorBuilder;
        $this->nodeRepresentationService = $nodeRepresentationService;
        $this->processorRunner = $processorRunner;
        $this->sortService = $sortService;

        parent::__construct(self::NAME);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    public function run(InputInterface $input, OutputInterface $output): int
    {
        $output->write(['<command-start>Starting most-used-conditions command</command-start>'], ['']);

        $directory = realpath($input->getArgument('directory'));
        if (false === $directory) {
            throw new \RuntimeException(sprintf(
                'Directory %s does not exist',
                $input->getArgument('directory')
            ));
        }

        $finderProgressBar = $this->createProgressBar($output, 'customFinder');

        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);

        $finder = $this->finderBuilder
            ->setDirectories([$directory])
            ->setIncludeFiles($this->getArrayOption('include-files', $input))
            ->setExcludes($this->getArrayOption('excludes', $input))
            ->setExcludePaths($this->getArrayOption('exclude-paths', $input))
            ->setExcludeFiles($this->getArrayOption('exclude-files', $input))
            ->setSuffixes($this->getArrayOption('suffixes', $input))
            ->getFinder();

        $files = $this->processFinder($finder, $finderProgressBar);

        $this->finishProgressBar($finderProgressBar, $output);

        if (0 === count($files)) {
            $output->writeln('<error>No files found</error>');

            return Application::EXIT_CODE_FAILURE;
        }

        $traverser = new Traverser();
        $visitors = $this->visitorBuilder->setNames($input->getOption('visitors'))->getVisitors();
        foreach ($visitors as $visitor) {
            $output->writeln('Adding '.$this->getClassnameWithoutNamespace(get_class($visitor)).' Visitor');
            $traverser->addVisitor($visitor);
        }

        $traverserProgressBar = $this->createProgressBar($output, 'customBar', 100);
        $traverserProgressBar->setMaxSteps(count($files));

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
                'Visitors are checking for conditions in files. Condition count: %d',
                $traverser->getNodeOccurrencesCount()
            ));
        }

        $this->finishProgressBar($traverserProgressBar, $output);

        $occurrenceList = new OccurrenceList();

        /** @var AbstractVisitor $visitor */
        foreach ($visitors as $visitor) {
            /** @var Occurrence $occurrence */
            foreach ($visitor->getNodeOccurrenceList()->getOccurrences() as $occurrence) {
                $occurrenceList->addOccurrence($occurrence);
            }
        }

        $processors = $this->processorBuilder->setNames($input->getOption('processors'))->getProcessors();

        if (count($processors)) {
            foreach ($processors as $processor) {
                $output->writeln('Adding '.$this->getClassnameWithoutNamespace(get_class($processor)).' Processor');
                $this->processorRunner->addProcessor($processor);
            }

            $processorsProgressBar = $this->createProgressBar($output, 'customBar', 100);

            foreach ($processorsProgressBar->iterate(
                $this->processorRunner->process($occurrenceList),
                count($processors)
            ) as $processorsDone) {
                $processorsProgressBar->setMessage(sprintf(
                    'Processor %d is processing conditions. Condition count: %d',
                    $processorsDone,
                    count($occurrenceList->getOccurrences())
                ));
            }

            $this->finishProgressBar($processorsProgressBar, $output);
        }

        $flipChecking = $input->getOption('with-flip-check');
        $conditionListProgressBar = $this->createProgressBar($output, 'customBar', 100);
        $conditionListProgressBar->setMaxSteps(count($occurrenceList->getOccurrences()));
        $conditionListProgressBar->setMessage(sprintf(
            'Create ConditionList (print ast nodes). Flip-Check: %s',
            $flipChecking ? 'active' : 'inactive'
        ));

        /** @var ConditionList|FlipChecking $conditionList */
        $conditionList = $this->conditionListBuilder
            ->setIsFlipCheckingAware($flipChecking)
            ->getConditionList();

        $flippedConditionCounter = 0;
        /** @var Occurrence $occurrence */
        foreach ($occurrenceList->getOccurrences() as $occurrence) {
            $representation = $this->nodeRepresentationService->representationForNode($occurrence->getNode());
            $condition = new Condition($representation, $occurrence);
            $conditionList->addCondition($condition);

            $conditionListProgressBar->advance();
            if ($flipChecking && $condition->getCondition() !== $representation) {
                $flippedConditionCounter++;
                $conditionListProgressBar->setMessage(
                    sprintf(
                        'Create ConditionList (print ast nodes). Flip-Check: %s. Flipped conditions: %d',
                        $flipChecking ? 'active' : 'inactive',
                        $flippedConditionCounter
                    )
                );
            }
        }

        $this->finishProgressBar($conditionListProgressBar, $output);

        $rawConditions = $conditionList->getConditions();
        $countedConditionsList = new Countable();

        $countedListProgressBar = $this->createProgressBar($output, 'customBar', 100);
        $countedListProgressBar->setMaxSteps(count($rawConditions));
        $countedListProgressBar->setMessage('Check for multiple conditions');

        foreach ($rawConditions as $condition) {
            $countedConditionsList->addCondition($condition);

            $countedListProgressBar->advance();
            $countedListProgressBar->setMessage(sprintf(
                'Check for multiple conditions. Unique conditions: %d',
                count($countedConditionsList->getCountedConditions())
            ));
        }

        $this->finishProgressBar($countedListProgressBar, $output);

        $maximumEntries = $this->getMaximumEntries($input);
        $sortDirection = $this->getSort($input);

        $countedConditions = $countedConditionsList->getCountedConditions();

        $output->writeln(sprintf(
            '<info>Sort Conditions by number of occurrences %s.</info>',
            $sortDirection
        ));

        $calculatedFirstResult = null;
        $calculatedMaxResults = null;
        if (null !== $maximumEntries) {
            if ('desc' === strtolower($sortDirection)) {
                $calculatedMaxResults = $maximumEntries;
            } else {
                $calculatedFirstResult = $countedConditions->count() - $maximumEntries;
            }
        }

        $sortConfiguration = $this->sortConfigurationBuilder
            ->setMaximumEntries($calculatedMaxResults)
            ->setFirstResult($calculatedFirstResult)
            ->addSortField('count', $sortDirection)
            ->addSortField('condition', 'ASC')
            ->getSortConfiguration();

        $sortedConditions = $this->sortService->sortArrayCollection($countedConditions, $sortConfiguration);

        $hideOccurrences = $input->getOption('hide-occurrences');
        $maximumOccurrences = $input->getOption('max-occurrences');
        $minOccurrences = $input->getOption('min-occurrences');
        if (null !== $minOccurrences) {
            $minOccurrences = (int) $minOccurrences;
            $output->writeln(sprintf(
                '<info>Just showing conditions with at least %d occurrences</info>',
                $minOccurrences
            ));
        }
        if (null !== $maximumEntries) {
            $output->writeln(sprintf(
                '<info>Just showing maximum %d conditions.</info>',
                $maximumEntries
            ));
        }
        if (null !== $maximumOccurrences) {
            $maximumOccurrences = (int) $maximumOccurrences;
        }

        $table = new Table($output);
        $table->setColumnMaxWidth(0, 100);
        $table->setHeaders([
            'condition',
            'count',
        ]);

        /** @var CountedCondition $countedCondition */
        foreach ($sortedConditions as $countedCondition) {
            if (null !== $minOccurrences && $minOccurrences > $countedCondition->getCount()) {
                continue;
            }
            $table->addRow([
                sprintf('<focus>%s</focus>', $countedCondition->getCondition()),
                $countedCondition->getCount(),
            ]);
            if ($hideOccurrences) {
                continue;
            }
            /** @var Occurrence $occurrence */
            $counter = 1;
            foreach ($countedCondition->getOccurrences() as $occurrence) {
                if (null !== $maximumOccurrences && $counter > $maximumOccurrences) {
                    break;
                }
                $line = $occurrence->getNode()->getStartLine();
                if ($occurrence->getNode()->getStartLine() !== $occurrence->getNode()->getEndLine()) {
                    $line .= sprintf('-%s', $occurrence->getNode()->getEndLine());
                }
                $flags = [];
                if ($occurrence->isFlipped()) {
                    $flags[] = 'flipped';
                }
                $table->addRow([
                    sprintf(
                        '%s:%s <flag>%s</flag>',
                        $occurrence->getFile()->getRelativePathname(),
                        $line,
                        0 === count($flags) ? '' : '(' . implode(',', $flags) . ')'
                    ),
                    '',
                ]);
                $counter++;
            }
            $table->addRow([new TableSeparator(), new TableSeparator()]);
        }

        $table->render();

        echo PHP_EOL;

        return Application::EXIT_CODE_SUCCESS;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName(self::NAME)
            ->setHelp('Find out your most used conditions')
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the user.')
            ->setDefinition(
                new InputDefinition([
                    new InputArgument(
                        'directory',
                        InputArgument::OPTIONAL,
                        'path to directory which should be checked',
                        getcwd()
                    ),
                ])
            )
            ->addOption(
                'excludes',
                null,
                InputOption::VALUE_REQUIRED,
                'Comma-separated string of directories-names which should be excluded (must be relative to source)',
                'vendor'
            )
            ->addOption(
                'exclude-paths',
                null,
                InputOption::VALUE_REQUIRED,
                'Comma-separated string of directories-paths which should be excluded (must be relative to source)',
                ''
            )
            ->addOption(
                'exclude-files',
                null,
                InputOption::VALUE_REQUIRED,
                'Comma-separated string of files which should be excluded (must be relative to source)',
                ''
            )
            ->addOption(
                'include-files',
                null,
                InputOption::VALUE_REQUIRED,
                'Comma-separated string of files which should be included (must be relative to source)',
                ''
            )
            ->addOption(
                'suffixes',
                null,
                InputOption::VALUE_REQUIRED,
                'Comma-separated string of valid source code filename extensions',
                'php'
            )
            ->addOption(
                'visitors',
                null,
                InputOption::VALUE_REQUIRED,
                'Comma-separated string of visitors which should check the source code to 
                 find conditions (on wrong input you can see a list of possible visitors)',
                'If,ElseIf,Ternary'
            )
            ->addOption(
                'processors',
                null,
                InputOption::VALUE_REQUIRED,
                'Comma-separated string of processors which should transform the conditions
                  (on wrong input you can see a list of possible processor)',
                ''
            )
            ->addOption(
                'sort',
                null,
                InputOption::VALUE_REQUIRED,
                'sort direction of conditions, desc or asc',
                'asc'
            )
            ->addOption(
                'max-entries',
                null,
                InputOption::VALUE_OPTIONAL,
                'maximum entries'
            )
            ->addOption(
                'max-occurrences',
                null,
                InputOption::VALUE_REQUIRED,
                'maximum occurrences'
            )
            ->addOption(
                'min-occurrences',
                null,
                InputOption::VALUE_REQUIRED,
                'minimum occurrences'
            )
            ->addOption(
                'with-flip-check',
                null,
                InputOption::VALUE_NONE,
                'flip checking conditions'
            )
            ->addOption(
                'without-occurrences',
                null,
                InputOption::VALUE_NONE,
                'hide occurrences'
            );
    }

    /**
     * @param string         $name
     * @param InputInterface $input
     *
     * @return array
     */
    private function getArrayOption(string $name, InputInterface $input): array
    {
        if ('' === $inputOption = $input->getOption($name)) {
            return [];
        }

        return explode(',', $inputOption);
    }

    /**
     * @param string $classname
     *
     * @return string
     */
    private function getClassnameWithoutNamespace(string $classname): string
    {
        $classWithNamespaces = explode('\\', $classname);

        return end($classWithNamespaces);
    }

    /**
     * @param OutputInterface $output
     * @param string          $format
     * @param int             $max
     *
     * @return ProgressBar
     */
    private function createProgressBar(OutputInterface $output, string $format, int $max = 0): ProgressBar
    {
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
    private function finishProgressBar(ProgressBar $progressBar, OutputInterface $output): void
    {
        $progressBar->setFormat('messageDuration');
        $progressBar->finish();
        $output->writeln('');
    }

    /**
     * @param InputInterface $input
     *
     * @return string
     */
    private function getSort(InputInterface $input): string
    {
        return $input->getOption('sort');
    }

    /**
     * @param Finder      $finder
     * @param ProgressBar $progressBar
     *
     * @return array
     */
    private function processFinder(Finder $finder, ProgressBar $progressBar): array
    {
        $files = [];

        /** @var SplFileInfo $file */
        foreach ($progressBar->iterate($finder->getIterator()) as $file) {
            $files[] = $file;
            $progressBar->setMessage(sprintf('(%s)', $file->getRelativePathname()), 'filename');
            $progressBar->setMessage(sprintf('Looking for Files. File count: %d', count($files)));
        }

        return $files;
    }

    /**
     * @param InputInterface $input
     *
     * @return int|null
     */
    private function getMaximumEntries(InputInterface $input): ?int
    {
        $maximumEntries = $input->getOption('max');
        if (null !== $maximumEntries) {
            $maximumEntries = (int) $maximumEntries;
        }

        return $maximumEntries;
    }
}
