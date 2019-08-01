<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Processor;

use Isfett\PhpAnalyzer\DAO\NodeOccurrenceList;
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
     * @param NodeOccurrenceList $nodeOccurrenceList
     *
     * @return void
     */
    public function setNodeOccurrenceList(NodeOccurrenceList $nodeOccurrenceList): void;
}
