<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Processor;

use Isfett\PhpAnalyzer\DAO\OccurrenceList;
use Isfett\PhpAnalyzer\DAO\Occurrence;

/**
 * Interface ProcessorInterface
 */
interface ProcessorInterface
{
    /**
     * @param Occurrence $occurrence
     *
     * @return void
     */
    public function process(Occurrence $occurrence): void;

    /**
     * @param OccurrenceList $nodeOccurrenceList
     *
     * @return void
     */
    public function setNodeOccurrenceList(OccurrenceList $nodeOccurrenceList): void;
}
