<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Processor\Condition;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\AbstractProcessor;
use PhpParser\Node;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\BinaryOp;

/**
 * Class SplitLogicalOperatorProcessor
 */
class SplitLogicalOperatorProcessor extends AbstractProcessor
{
    /**
     * @param Occurrence $occurrence
     *
     * @return void
     */
    public function process(Occurrence $occurrence): void
    {
        $isBooleanNot = false;
        $node = $occurrence->getNode();
        if ($this->isBooleanNotNestedLogicalOperator($node)) {
            /** @var Node\Expr\BooleanNot $node */
            $node = $node->expr;
            $isBooleanNot = true;
        }
        if ($this->isLogicalOperator($node)) {
            /** @var BinaryOp $node */
            $this->createLogicalOperatorOccurrences($occurrence, $node, $isBooleanNot);

            $this->nodeOccurrenceList->removeOccurrence($occurrence);
        }
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    private function isLogicalOperator(Node $node): bool
    {
        return $this->isLogicalOperatorBoolean($node) || $this->isLogicalOperatorLogical($node);
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    private function isBooleanNotNestedLogicalOperator(Node $node): bool
    {
        return $node instanceof BooleanNot && $this->isLogicalOperator($node->expr);
    }

    /**
     * @param Node\Expr $originalNode
     * @param bool      $isBooleanNot
     *
     * @return Node|BooleanNot|BinaryOp
     */
    private function createNewLogicalOperatorNode(Node\Expr $originalNode, bool $isBooleanNot): Node
    {
        $node = clone $originalNode;

        if ($isBooleanNot) {
            $node = $this->negate($node);
        }

        return $node;
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    private function isLogicalOperatorBoolean(Node $node): bool
    {
        return $node instanceof Node\Expr\BinaryOp\BooleanAnd ||
            $node instanceof Node\Expr\BinaryOp\BooleanOr;
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    private function isLogicalOperatorLogical(Node $node): bool
    {
        return $node instanceof Node\Expr\BinaryOp\LogicalAnd ||
            $node instanceof Node\Expr\BinaryOp\LogicalOr;
    }

    /**
     * @param Occurrence $originalOccurrence
     * @param BinaryOp   $originalNode
     * @param bool       $isBooleanNot
     *
     * @return void
     */
    private function createLogicalOperatorOccurrences(
        Occurrence $originalOccurrence,
        BinaryOp $originalNode,
        bool $isBooleanNot
    ): void {
        foreach (['left', 'right'] as $operatorSide) {
            $sideNode = $originalNode->$operatorSide;

            $node = $this->createNewLogicalOperatorNode($sideNode, $isBooleanNot);
            $occurrence = clone $originalOccurrence;
            $occurrence->setNode($node);
            $this->markOccurrenceAsAffected($occurrence);
            $this->nodeOccurrenceList->addOccurrence($occurrence);

            $this->process($occurrence);
        }
    }
}
