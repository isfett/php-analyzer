<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node;

use Isfett\PhpAnalyzer\DAO\OccurrenceList;

/**
 * Interface ProcessorRunnerInterface
 */
interface ProcessorRunnerInterface
{
    /**
     * @param OccurrenceList $nodeOccurrenceList
     *
     * @return \Generator
     */
    public function process(OccurrenceList $nodeOccurrenceList): \Generator;
}
