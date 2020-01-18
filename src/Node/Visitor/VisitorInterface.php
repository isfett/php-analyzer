<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Visitor;

use Isfett\PhpAnalyzer\DAO\OccurrenceList;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Interface VisitorInterface
 */
interface VisitorInterface
{
    /**
     * @return OccurrenceList
     */
    public function getNodeOccurrenceList(): OccurrenceList;

    /**
     * @param SplFileInfo $file
     *
     * @return void
     */
    public function setFile(SplFileInfo $file): void;
}
