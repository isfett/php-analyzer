<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node;

use Isfett\PhpAnalyzer\DAO\OccurrenceList;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr;

/**
 * Class AbstractProcessor
 */
abstract class AbstractProcessor implements Processor\ProcessorInterface
{
    /** @var OccurrenceList */
    protected $nodeOccurrenceList;

    /**
     * @param OccurrenceList $nodeOccurrenceList
     */
    public function setNodeOccurrenceList(OccurrenceList $nodeOccurrenceList): void
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
