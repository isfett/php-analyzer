<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Visitor;

use Isfett\PhpAnalyzer\Node\AbstractVisitor;
use PhpParser\Node;

/**
 * Class CoalesceConditionVisitor
 */
class CoalesceConditionVisitor extends AbstractVisitor
{
    /**
     * @param Node $node
     *
     * @return int|null
     */
    public function enterNode(Node $node): ?int
    {
        if ($node instanceof Node\Expr\BinaryOp\Coalesce) {
            $this->addNodeOccurrence($node->left);
        }

        return null;
    }
}