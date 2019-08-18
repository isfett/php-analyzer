<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Visitor\MagicString;

use Isfett\PhpAnalyzer\Node\AbstractVisitor;
use PhpParser\Node;

/**
 * Class ConditionVisitor
 */
class ConditionVisitor extends AbstractVisitor
{
    /**
     * @param Node $node
     *
     * @return int|null
     */
    public function enterNode(Node $node): ?int
    {
        if ($this->isString($node) &&
            $this->isCondition($node) &&
            $this->notComparingAgainstConst($node)
        ) {
            $this->addNodeOccurrence($node);
        }

        return null;
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    private function isCondition(Node $node): bool
    {
        $possibleConditions = [
            Node\Expr\BinaryOp\Coalesce::class,
            Node\Expr\BinaryOp\Equal::class,
            Node\Expr\BinaryOp\Greater::class,
            Node\Expr\BinaryOp\GreaterOrEqual::class,
            Node\Expr\BinaryOp\Identical::class,
            Node\Expr\BinaryOp\LogicalAnd::class,
            Node\Expr\BinaryOp\LogicalOr::class,
            Node\Expr\BinaryOp\LogicalXor::class,
            Node\Expr\BinaryOp\NotEqual::class,
            Node\Expr\BinaryOp\NotIdentical::class,
            Node\Expr\BinaryOp\Smaller::class,
            Node\Expr\BinaryOp\SmallerOrEqual::class,
            Node\Expr\BinaryOp\Spaceship::class,
        ];
        /** @var Node $parent */
        $parentNode = $node->getAttribute('parent');

        return in_array(get_class($parentNode), $possibleConditions, true);
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    private function notComparingAgainstConst(Node $node): bool
    {
        /** @var Node $parent */
        $parentNode = $node->getAttribute('parent');

        return $parentNode instanceof Node\Expr\BinaryOp &&
            !$parentNode->left instanceof Node\Expr\ConstFetch &&
            !$parentNode->right instanceof Node\Expr\ConstFetch;
    }
}
