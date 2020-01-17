<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Processor\Condition;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\AbstractProcessor;
use PhpParser\Node;

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

        // phpcs:ignore SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
        if ($this->isLogicalOperator($node)) {
            /** @var Node\Expr\BinaryOp $node */
            $this->createLogicalOperatorOccurrences($occurrence, $node, $isBooleanNot);

            $this->nodeOccurrenceList->removeOccurrence($occurrence);
        }
    }

    /**
     * @param Occurrence         $originalOccurrence
     * @param Node\Expr\BinaryOp $originalNode
     * @param bool               $isBooleanNot
     *
     * @return void
     */
    private function createLogicalOperatorOccurrences(
        Occurrence $originalOccurrence,
        Node\Expr\BinaryOp $originalNode,
        bool $isBooleanNot
    ): void {
        foreach (self::BINARY_OP_SIDES as $operatorSide) {
            $sideNode = $originalNode->$operatorSide;

            $node = $this->createNewLogicalOperatorNode($sideNode, $isBooleanNot);
            $occurrence = clone $originalOccurrence;
            $occurrence->setNode($node);
            $this->markOccurrenceAsAffected($occurrence);
            $this->nodeOccurrenceList->addOccurrence($occurrence);

            $this->process($occurrence);
        }
    }

    /**
     * @param Node\Expr $originalNode
     * @param bool      $isBooleanNot
     *
     * @return Node|Node\Expr\BooleanNot|Node\Expr\BinaryOp
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
    private function isBooleanNotNestedLogicalOperator(Node $node): bool
    {
        return $node instanceof Node\Expr\BooleanNot && $this->isLogicalOperator($node->expr);
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
}
