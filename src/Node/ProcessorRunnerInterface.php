<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node;

use Isfett\PhpAnalyzer\DAO\NodeOccurrenceList;

/**
 * Interface ProcessorRunnerInterface
 */
interface ProcessorRunnerInterface
{
    /**
     * @param NodeOccurrenceList $nodeOccurrenceList
     *
     * @return \Generator
     */
    public function process(NodeOccurrenceList $nodeOccurrenceList): \Generator;
}
