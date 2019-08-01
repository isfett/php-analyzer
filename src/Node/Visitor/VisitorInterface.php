<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Visitor;

use Isfett\PhpAnalyzer\DAO\NodeOccurrenceList;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Interface VisitorInterface
 */
interface VisitorInterface
{
    /**
     * @param SplFileInfo $file
     *
     * @return void
     */
    public function setFile(SplFileInfo $file): void;

    /**
     * @return NodeOccurrenceList
     */
    public function getNodeOccurrenceList(): NodeOccurrenceList;
}
