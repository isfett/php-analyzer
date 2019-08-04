<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Processor;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\AbstractProcessor;
use PhpParser\Node;

/**
 * Class RemoveAssignmentProcessor
 */
class RemoveAssignmentProcessor extends AbstractProcessor
{
    /**
     * @param Occurrence $occurrence
     *
     * @return void
     */
    public function process(Occurrence $occurrence): void
    {
        /** @var Node $node */
        $node = $occurrence->getNode();
        if ($node instanceof Node\Expr\BinaryOp) {
            $node = $this->replaceAssignmentInBinaryOp($node);
        } else {
            $node = $this->replaceAssignment($node);
        }
        $occurrence->setNode($node);
    }

    /**
     * @param Node $node
     *
     * @return Node
     */
    private function replaceAssignment(Node $node): Node
    {
        if ($node instanceof Node\Expr\Assign) {
            $node = $node->expr;
        }

        return $node;
    }

    /**
     * @param Node\Expr\BinaryOp $node
     *
     * @return Node\Expr\BinaryOp
     */
    private function replaceAssignmentInBinaryOp(Node\Expr\BinaryOp $node): Node\Expr\BinaryOp
    {
        foreach (['left', 'right'] as $binaryOpSide) {
            if ($node->$binaryOpSide instanceof Node\Expr\BinaryOp) {
                $node->$binaryOpSide = $this->replaceAssignmentInBinaryOp($node->$binaryOpSide);
            }
            $node->$binaryOpSide = $this->replaceAssignment($node->$binaryOpSide);
        }

        return $node;
    }
}
