<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node;

use Isfett\PhpAnalyzer\DAO\NodeOccurrenceList;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr;

/**
 * Class AbstractProcessor
 */
abstract class AbstractProcessor implements Processor\ProcessorInterface
{
    /** @var NodeOccurrenceList */
    protected $nodeOccurrenceList;

    /**
     * @param NodeOccurrenceList $nodeOccurrenceList
     */
    public function setNodeOccurrenceList(NodeOccurrenceList $nodeOccurrenceList): void
    {
        $this->nodeOccurrenceList = $nodeOccurrenceList;
    }

    /**
     * @param Expr $node
     *
     * @return BooleanNot
     */
    protected function negate(Expr $node): BooleanNot
    {
        return new BooleanNot($node, $node->getAttributes());
    }
}
