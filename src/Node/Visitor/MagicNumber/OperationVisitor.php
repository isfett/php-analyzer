<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Visitor\MagicNumber;

use Isfett\PhpAnalyzer\Node\AbstractVisitor;
use PhpParser\Node;

/**
 * Class OperationVisitor
 */
class OperationVisitor extends AbstractVisitor
{
    /**
     * @param Node $node
     *
     * @return int|null
     */
    public function enterNode(Node $node): ?int
    {
        if ($this->isNumber($node) && $this->isOperation($node)) {
            $this->addNodeOccurrence($node);
        }

        return null;
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    private function isOperation(Node $node): bool
    {
        $possibleOperations = [
            Node\Expr\BinaryOp\Div::class,
            Node\Expr\BinaryOp\Minus::class,
            Node\Expr\BinaryOp\Mod::class,
            Node\Expr\BinaryOp\Mul::class,
            Node\Expr\BinaryOp\Plus::class,
            Node\Expr\BinaryOp\Pow::class,
            Node\Expr\BinaryOp\ShiftLeft::class,
            Node\Expr\BinaryOp\ShiftRight::class,
        ];
        /** @var Node $parent */
        $parentNode = $node->getAttribute(self::NODE_ATTRIBUTE_PARENT);

        return in_array(get_class($parentNode), $possibleOperations, true);
    }
}
