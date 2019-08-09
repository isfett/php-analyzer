<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Processor;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\AbstractProcessor;
use PhpParser\Node;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\Isset_;

/**
 * Class SplitIssetProcessor
 */
class SplitIssetProcessor extends AbstractProcessor
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
        if ($this->isBooleanNotNestedIsset($node)) {
            /** @var Node\Expr\BooleanNot $node */
            $node = $node->expr;
            $isBooleanNot = true;
        }
        if ($this->isIssetAndHasMultipleArguments($node)) {
            /** @var Isset_ $node */
            $arguments = $node->vars;
            $node->vars = [];

            $this->createIssetOccurrences($occurrence, $arguments, $node, $isBooleanNot);
            $this->nodeOccurrenceList->removeOccurrence($occurrence);
        }
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    private function isIssetAndHasMultipleArguments(Node $node): bool
    {
        return $node instanceof Isset_ && count($node->vars) > 1;
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    private function isBooleanNotNestedIsset(Node $node): bool
    {
        return $node instanceof BooleanNot && $this->isIssetAndHasMultipleArguments($node->expr);
    }

    /**
     * @param Isset_ $originalNode
     * @param Node   $var
     * @param bool   $isBooleanNot
     *
     * @return Node|Isset_|BooleanNot
     */
    private function createNewIssetNode(Isset_ $originalNode, Node $var, bool $isBooleanNot): Node
    {
        $node = clone $originalNode;
        $node->vars[] = $var;

        if ($isBooleanNot) {
            $node = $this->negate($node);
        }

        return $node;
    }

    /**
     * @param Occurrence $occurrence
     * @param iterable   $arguments
     * @param Isset_     $node
     * @param bool       $isBooleanNot
     *
     * @return void
     */
    private function createIssetOccurrences(
        Occurrence $originalOccurrence,
        iterable $arguments,
        Isset_ $originalNode,
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
}
