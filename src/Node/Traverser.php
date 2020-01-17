<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node;

use Isfett\PhpAnalyzer\Node\Visitor\VisitorInterface;
use PhpParser\NodeTraverser;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class Traverser
 */
class Traverser extends NodeTraverser
{
    /** @var int */
    private const COUNTER_START = 0;

    /**
     * @return int
     */
    public function getNodeOccurrencesCount(): int
    {
        $occurrenceCounter = self::COUNTER_START;

        foreach ($this->visitors as $visitor) {
            if (!$visitor instanceof VisitorInterface) {
                continue;
            }

            $occurrenceCounter += $visitor->getNodeOccurrenceList()->count();
        }

        return $occurrenceCounter;
    }

    /**
     * @param SplFileInfo $file
     */
    public function setFile(SplFileInfo $file): void
    {
        $this->updateFileInVisitors($file);
    }

    /**
     * @param SplFileInfo $file
     *
     * @return void
     */
    private function updateFileInVisitors(SplFileInfo $file): void
    {
        foreach ($this->visitors as $visitor) {
            if (!$visitor instanceof VisitorInterface) {
                continue;
            }

            $visitor->setFile($file);
        }
    }
}
