<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Visitor\Condition;

use Isfett\PhpAnalyzer\Node\AbstractVisitor;
use PhpParser\Node;

/**
 * Class ElseIfVisitor
 */
class ElseIfVisitor extends AbstractVisitor
{
    /**
     * @param Node $node
     *
     * @return int|null
     */
    public function enterNode(Node $node): ?int
    {
        if ($node instanceof Node\Stmt\If_) {
            foreach ($node->elseifs as $elseif) {
                $this->addNodeOccurrence($elseif->cond);
            }
        }

        return null;
    }
}
