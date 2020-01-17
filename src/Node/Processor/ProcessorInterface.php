<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Processor;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\DAO\OccurrenceList;

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
