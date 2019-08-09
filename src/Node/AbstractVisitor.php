<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node;

use Isfett\PhpAnalyzer\DAO\OccurrenceList;
use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\Visitor\VisitorInterface;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class AbstractVisitor
 */
abstract class AbstractVisitor extends NodeVisitorAbstract implements VisitorInterface
{
    /** @var SplFileInfo */
    private $file;

    /** @var OccurrenceList */
    private $nodeOccurrenceList;

    /**
     * Visitor constructor.
     */
    public function __construct()
    {
        $this->nodeOccurrenceList = new OccurrenceList();
    }

    /**
     * @return OccurrenceList
     */
    public function getNodeOccurrenceList(): OccurrenceList
    {
        return $this->nodeOccurrenceList;
    }

    /**
     * @param SplFileInfo $file
     */
    public function setFile(SplFileInfo $file): void
    {
        $this->file = $file;
    }

    /**
     * @param Node $node
     *
     * @return void
     */
    protected function addNodeOccurrence(Node $node): void
    {
        $occurrence = new Occurrence($node, $this->file);
        $this->nodeOccurrenceList->addOccurrence($occurrence);
    }
}
