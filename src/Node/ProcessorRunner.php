<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\DAO\OccurrenceList;

/**
 * Class ProcessorRunner
 */
class ProcessorRunner implements ProcessorRunnerInterface
{
    /** @var array */
    private $processors = [];

    /**
     * @param Processor\ProcessorInterface $processor
     *
     * @return void
     */
    public function addProcessor(Processor\ProcessorInterface $processor): void
    {
        $this->processors[] = $processor;
    }

    /**
     * @param OccurrenceList $nodeOccurrenceList
     *
     * @return \Generator
     */
    public function process(OccurrenceList $nodeOccurrenceList): \Generator
    {
        /** @var Processor\ProcessorInterface $processor */
        foreach ($this->processors as $counter => $processor) {
            $counter++;
            $processor->setNodeOccurrenceList($nodeOccurrenceList);
            /** @var Occurrence $occurrence */
            foreach ($nodeOccurrenceList->getOccurrences() as $occurrence) {
                $processor->process($occurrence);
            }

            yield $counter;
        }
    }
}
