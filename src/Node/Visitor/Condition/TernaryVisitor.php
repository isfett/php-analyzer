<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Visitor\Condition;

use Isfett\PhpAnalyzer\Node\AbstractVisitor;
use PhpParser\Node;

/**
 * Class TernaryVisitor
 */
class TernaryVisitor extends AbstractVisitor
{
    /**
     * @param Node $node
     *
     * @return int|null
     */
    public function enterNode(Node $node): ?int
    {
        if ($node instanceof Node\Expr\Ternary) {
            $this->addNodeOccurrence($node->cond);
        }

        return null;
    }
}
