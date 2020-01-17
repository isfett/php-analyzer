<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Visitor\Condition;

use Isfett\PhpAnalyzer\Node\AbstractVisitor;
use PhpParser\Node;

/**
 * Class BooleanReturnVisitor
 */
class BooleanReturnVisitor extends AbstractVisitor
{
    /** @var string */
    private const RETURN_TYPE_BOOL = 'bool';

    /**
     * @param Node $node
     *
     * @return int|null
     */
    public function enterNode(Node $node): ?int
    {
        if ($node instanceof Node\Stmt\Function_) {
            /** @var Node\Identifier|Node\Name|null $returnType */
            $returnType = $node->returnType;
            if ($returnType instanceof Node\Identifier &&
                self::RETURN_TYPE_BOOL === strtolower($returnType->name) &&
                count($node->stmts)
            ) {
                /** @var Node\Stmt\Return_ $return */
                $return = end($node->stmts);
                $this->addNodeOccurrence($return->expr);
            }
        }

        return null;
    }
}
