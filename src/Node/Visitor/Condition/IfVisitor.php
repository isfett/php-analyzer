<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Visitor\Condition;

use Isfett\PhpAnalyzer\Node\AbstractVisitor;
use PhpParser\Node;

/**
 * Class IfVisitor
 */
class IfVisitor extends AbstractVisitor
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
