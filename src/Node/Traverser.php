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
    /**
     * @param SplFileInfo $file
     */
    public function setFile(SplFileInfo $file): void
    {
        $this->updateFileInVisitors($file);
    }

    /**
     * @return int
     */
    public function getNodeOccurrencesCount(): int
    {
        $count = 0;

        foreach ($this->visitors as $visitor) {
            if ($visitor instanceof VisitorInterface) {
                $count += $visitor->getNodeOccurrenceList()->count();
            }
        }

        return $count;
    }

    /**
     * @param SplFileInfo $file
     *
     * @return void
     */
    private function updateFileInVisitors(SplFileInfo $file): void
    {
        foreach ($this->visitors as $visitor) {
            if ($visitor instanceof VisitorInterface) {
                $visitor->setFile($file);
            }
        }
    }
}
