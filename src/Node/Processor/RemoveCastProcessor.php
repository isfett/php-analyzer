<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Processor;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\AbstractProcessor;
use PhpParser\Node;

/**
 * Class RemoveCastProcessor
 */
class RemoveCastProcessor extends AbstractProcessor
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
            $node = $this->replaceCastInBinaryOp($node, $occurrence);
        } else {
            $node = $this->replaceCast($node, $occurrence);
        }
        $occurrence->setNode($node);
    }

    /**
     * @param Node       $node
     * @param Occurrence $occurrence
     *
     * @return Node
     */
    private function replaceCast(Node $node, Occurrence $occurrence): Node
    {
        if ($node instanceof Node\Expr\Cast) {
            $node = $node->expr;
            $this->markOccurrenceAsAffected($occurrence);
        }

        return $node;
    }

    /**
     * @param Node\Expr\BinaryOp $node
     * @param Occurrence         $occurrence
     *
     * @return Node\Expr\BinaryOp
     */
    private function replaceCastInBinaryOp(Node\Expr\BinaryOp $node, Occurrence $occurrence): Node\Expr\BinaryOp
    {
        foreach (['left', 'right'] as $binaryOpSide) {
            if ($node->$binaryOpSide instanceof Node\Expr\BinaryOp) {
                $node->$binaryOpSide = $this->replaceCastInBinaryOp($node->$binaryOpSide, $occurrence);
            }
            $node->$binaryOpSide = $this->replaceCast($node->$binaryOpSide, $occurrence);
        }

        return $node;
    }
}
