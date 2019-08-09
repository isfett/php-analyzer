<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Visitor;

use Isfett\PhpAnalyzer\Node\AbstractVisitor;
use PhpParser\Node;

/**
 * Class IfConditionVisitor
 */
class IfConditionVisitor extends AbstractVisitor
{
    /**
     * @param Node $node
     *
     * @return int|null
     */
    public function enterNode(Node $node): ?int
    {
        if ($node instanceof Node\Stmt\If_) {
            $this->addNodeOccurrence($node->cond);
        }

        return null;
    }
}
