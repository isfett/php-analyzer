<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Console\Command;

use Doctrine\Common\Collections\ArrayCollection;
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
use Isfett\PhpAnalyzer\Node\VisitorConnector\ParentConnector;
use Isfett\PhpAnalyzer\Service\NodeRepresentationService;
use Isfett\PhpAnalyzer\Service\SortService;
use PhpParser\Error;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\UnaryMinus;
use PhpParser\Node\Expr\UnaryPlus;
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
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * Class MagicNumberDetectorCommand
 */
class MagicNumberDetectorCommand extends Command
{
    /** @var string */
    private const NAME = 'magic-number-detector';

    /** @var FinderBuilderInterface */
    private $finderBuilder;

    /** @var VisitorBuilderInterface */
    private $visitorBuilder;

    /** @var ProcessorBuilderInterface */
    private $processorBuilder;

    /** @var ProcessorRunner */
    private $processorRunner;

    /** @var SortConfigurationBuilder */
    private $sortConfigurationBuilder;

    /** @var NodeRepresentationService */
    private $nodeRepresentationService;

    /** @var SortService */
    private $sortService;

    /**
     * MagicNumberDetectorCommand constructor.
     *
     * @param FinderBuilderInterface            $finderBuilder
     * @param VisitorBuilderInterface           $visitorBuilder
     * @param ProcessorBuilderInterface         $processorBuilder
     * @param SortConfigurationBuilderInterface $sortConfigurationBuilder
     * @param ProcessorRunnerInterface          $processorRunner
     * @param NodeRepresentationService         $nodeRepresentationService
     * @param SortService                       $sortService
     */
    public function __construct(
        FinderBuilderInterface $finderBuilder,
        VisitorBuilderInterface $visitorBuilder,
        ProcessorBuilderInterface $processorBuilder,
        SortConfigurationBuilderInterface $sortConfigurationBuilder,
        ProcessorRunnerInterface $processorRunner,
        NodeRepresentationService $nodeRepresentationService,
        SortService $sortService
    ) {
        $this->finderBuilder = $finderBuilder;
        $this->visitorBuilder = $visitorBuilder;
        $this->processorBuilder = $processorBuilder;
        $this->processorRunner = $processorRunner;
        $this->sortConfigurationBuilder = $sortConfigurationBuilder;
        $this->nodeRepresentationService = $nodeRepresentationService;
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
        $output->write(['<command-start>Starting magic-number-detector command</command-start>'], ['']);

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
        $traverser->addVisitor(new ParentConnector());
        $visitors = $this->visitorBuilder
            ->setPrefix('MagicNumber')
            ->setNames($input->getOption('visitors'))
            ->getVisitors();

        foreach ($visitors as $visitor) {
            $output->writeln('Adding '.$this->getClassnameWithoutNamespace(get_class($visitor)).' Visitor');
            $traverser->addVisitor($visitor);
        }

        $traverserProgressBar = $this->createProgressBar(
            $output,
            'customBar',
            Application::CONSOLE_TABLE_DEFAULT_MAX_WIDTH
        );
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
                'Visitors are checking for magic numbers in files. Magic numbers found: %d',
                $traverser->getNodeOccurrencesCount()
            ));
        }

        $traverserProgressBar->setMessage(sprintf(
            'Visitors checked magic numbers in %d files. Magic numbers found: %d',
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

        $processors = $this->processorBuilder
            ->setPrefix('MagicNumber')
            ->setNames($input->getOption('processors'))
            ->getProcessors();

        if (count($processors)) {
            foreach ($processors as $processor) {
                $output->writeln('Adding '.$this->getClassnameWithoutNamespace(get_class($processor)).' Processor');
                $this->processorRunner->addProcessor($processor);
            }

            $processorsProgressBar = $this->createProgressBar(
                $output,
                'customBar',
                Application::CONSOLE_TABLE_DEFAULT_MAX_WIDTH
            );

            foreach ($processorsProgressBar->iterate(
                $this->processorRunner->process($occurrenceList),
                count($processors)
            ) as $processorsDone) {
                $processorsProgressBar->setMessage(sprintf(
                    'Processor %d is processing magic numbers. Magic numbers found: %d',
                    $processorsDone,
                    count($occurrenceList->getOccurrences())
                ));
            }

            $processorsProgressBar->setMessage(sprintf(
                'Processors processed magic numbers. Magic numbers found: %d',
                count($occurrenceList->getOccurrences())
            ));
            $this->finishProgressBar($processorsProgressBar, $output);
        }

        if (0 === $occurrenceList->getOccurrences()->count()) {
            return Application::EXIT_CODE_SUCCESS;
        }

        $sortConfiguration = $this->sortConfigurationBuilder
            ->setMaxResults(null)
            ->setFirstResult(null)
            ->addSortField('value', $this->getSort($input))
            ->getSortConfiguration();

        $sortedOccurrences = $this->sortService->sortOccurrenceCollectionByNodeValues(
            $occurrenceList->getOccurrences(),
            $sortConfiguration
        );

        $table = new Table($output);
        $table->setColumnMaxWidth(0, Application::CONSOLE_TABLE_DEFAULT_MAX_WIDTH);
        $table->setHeaders([
            'Number',
            'Occurrence',
        ]);

        $occurrenceCounter = 0;
        /** @var Occurrence $occurrence */
        foreach ($sortedOccurrences as $occurrence) {
            $occurrenceCounter++;
            $line = $occurrence->getNode()->getStartLine();
            $node = $occurrence->getNode();
            $parent = $node->getAttribute('parent');
            $isMinus = false;
            if ($parent instanceof UnaryMinus) {
                $isMinus = true;
                $parent = $parent->getAttribute('parent');
            }
            if ($parent instanceof Arg) {
                $parent = $parent->getAttribute('parent');
            }
            if ($occurrence->getNode()->getStartLine() !== $occurrence->getNode()->getEndLine()) {
                $line .= sprintf('-%s', $occurrence->getNode()->getEndLine());
            }
            $representation = $this->nodeRepresentationService->representationForNode(
                $parent
            );
            $value = $node->value;
            if ($isMinus) {
                $value = '-'.$value;
            }
            $representation = $this->lreplace(
                (string) $value,
                '<focus>' . $value . '</focus>',
                $representation
            );
            $table->addRow([
                sprintf('%s', $representation),
                sprintf(
                    '<href=file://%s>%s:%s</>',
                    $occurrence->getFile()->getPathname(),
                    $occurrence->getFile()->getRelativePathname(),
                    $line
                ),
            ]);
            if ($occurrenceCounter < $occurrenceList->count()) {
                $table->addRow([new TableSeparator(), new TableSeparator()]);
            }
        }

        $table->render();

        $output->write(PHP_EOL);

        return Application::EXIT_CODE_FAILURE;
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
                'Assign,Condition,DefaultParameter,Operation,Property,Return,SwitchCase'
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
     * @param Finder      $finder
     * @param ProgressBar $progressBar
     *
     * @return array
     */
    private function processFinder(Finder $finder, ProgressBar $progressBar): array
    {
        $files = [];
        $progressBar->setMessage('Looking for files');

        /** @var SplFileInfo $file */
        foreach ($progressBar->iterate($finder->getIterator()) as $file) {
            $files[] = $file;
            $progressBar->setMessage(sprintf('(%s)', $file->getRelativePathname()), 'filename');
            $progressBar->setMessage(sprintf('Looking for files. File count: %d', count($files)));
        }

        return $files;
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
     * @param string $search
     * @param string $replace
     * @param string $subject
     *
     * @return string
     */
    private function lreplace(string $search, string $replace, string $subject): string
    {
        $pos = strrpos($subject, $search);
        if (false !== $pos) {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
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
}
