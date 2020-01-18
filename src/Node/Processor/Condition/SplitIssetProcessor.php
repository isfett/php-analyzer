<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Processor\Condition;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\AbstractProcessor;
use PhpParser\Node;

/**
 * Class SplitIssetProcessor
 */
class SplitIssetProcessor extends AbstractProcessor
{
    /** @var int */
    private const ONE = 1;

    /**
     * @param Occurrence $occurrence
     *
     * @return void
     */
    public function process(Occurrence $occurrence): void
    {
        $isBooleanNot = false;
        $node = $occurrence->getNode();
        if ($this->isBooleanNotNestedIsset($node)) {
            /** @var Node\Expr\BooleanNot $node */
            $node = $node->expr;
            $isBooleanNot = true;
        }

        // phpcs:ignore SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
        if ($this->isIssetAndHasMultipleArguments($node)) {
            /** @var Node\Expr\Isset_ $node */
            $arguments = $node->vars;
            $node->vars = [];

            $this->createIssetOccurrences($occurrence, $arguments, $node, $isBooleanNot);
            $this->nodeOccurrenceList->removeOccurrence($occurrence);
        }
    }

    /**
     * @param Occurrence       $originalOccurrence
     * @param iterable         $arguments
     * @param Node\Expr\Isset_ $originalNode
     * @param bool             $isBooleanNot
     *
     * @return void
     */
    private function createIssetOccurrences(
        Occurrence $originalOccurrence,
        iterable $arguments,
        Node\Expr\Isset_ $originalNode,
        bool $isBooleanNot
    ): void {
        foreach ($arguments as $argument) {
            $node = $this->createNewIssetNode($originalNode, $argument, $isBooleanNot);
            $occurrence = clone $originalOccurrence;
            $occurrence->setNode($node);
            $this->markOccurrenceAsAffected($occurrence);
            $this->nodeOccurrenceList->addOccurrence($occurrence);
        }
    }

    /**
     * @param Node\Expr\Isset_ $originalNode
     * @param Node             $var
     * @param bool             $isBooleanNot
     *
     * @return Node|Node\Expr\Isset_|Node\Expr\BooleanNot
     */
    private function createNewIssetNode(Node\Expr\Isset_ $originalNode, Node $var, bool $isBooleanNot): Node
    {
        $node = clone $originalNode;
        $node->vars[] = $var;

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
    private function isBooleanNotNestedIsset(Node $node): bool
    {
        return $node instanceof Node\Expr\BooleanNot && $this->isIssetAndHasMultipleArguments($node->expr);
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    private function isIssetAndHasMultipleArguments(Node $node): bool
    {
        return $node instanceof Node\Expr\Isset_ && count($node->vars) > self::ONE;
    }
}
