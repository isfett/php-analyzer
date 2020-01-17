<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Processor\Condition;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\AbstractProcessor;
use Isfett\PhpAnalyzer\Node\Expr\NegationInterface;
use PhpParser\Node;

/**
 * Class NegateBooleanNotProcessor
 */
class NegateBooleanNotProcessor extends AbstractProcessor
{
    /** @var string */
    private const FORMAT_TRANSFORM = '%s%s%s';

    /** @var string */
    private const NAMESPACE_BOOLEAN_NOT = 'BooleanNot';

    /**
     * @param Occurrence $occurrence
     *
     * @return void
     */
    public function process(Occurrence $occurrence): void
    {
        $node = $occurrence->getNode();
        if (!$this->isBooleanNotAndHasBinaryOpExpression($node)) {
            return;
        }

        /** @var Node\Expr\BooleanNot $node */
        /** @var BinaryOp $expression */
        $expression = $node->expr;

        $occurrence->setNode($this->processBinaryOp($expression, $occurrence));
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    private function isBooleanNotAndHasBinaryOpExpression(Node $node): bool
    {
        return $node instanceof Node\Expr\BooleanNot && $node->expr instanceof Node\Expr\BinaryOp;
    }

    /**
     * @param Node\Expr\BinaryOp $node
     * @param Occurrence         $occurrence
     *
     * @return Node\Expr\BinaryOp
     */
    private function processBinaryOp(Node\Expr\BinaryOp $node, Occurrence $occurrence): Node\Expr\BinaryOp
    {
        $negationClassname = $this->transformClassname(get_class($node));

        if (class_exists($negationClassname)) {
            $this->markOccurrenceAsAffected($occurrence);

            /** @var NegationInterface $negationClass */
            $negationClass = new $negationClassname();

            return $negationClass->negate($node);
        }

        $node = $this->processLogicalOp($node, $occurrence);

        return $node;
    }

    /**
     * @param Node\Expr\BinaryOp $node
     * @param Occurrence         $occurrence
     *
     * @return Node\Expr\BinaryOp
     */
    private function processLogicalOp(Node\Expr\BinaryOp $node, Occurrence $occurrence): Node\Expr\BinaryOp
    {
        foreach (self::BINARY_OP_SIDES as $binaryOpSide) {
            if ($this->isBooleanNotAndHasBinaryOpExpression($node->$binaryOpSide)) {
                $node->$binaryOpSide = $this->processBinaryOp($node->$binaryOpSide->expr, $occurrence);
            }

            // phpcs:ignore SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
            if ($node->$binaryOpSide instanceof Node\Expr\BinaryOp) {
                $node->$binaryOpSide = $this->processBinaryOp($node->$binaryOpSide, $occurrence);
            }
        }

        return $node;
    }

    /**
     * @param string $classname
     *
     * @return string
     */
    private function transformClassname(string $classname): string
    {
        $classWithNamespaces = explode(self::NAMESPACE_SEPARATOR, $classname);
        $classWithoutNamespace = end($classWithNamespaces);

        $targetNamespaces = explode(self::NAMESPACE_SEPARATOR, NegationInterface::class);
        array_pop($targetNamespaces);
        $targetNamespaces[] = self::NAMESPACE_BOOLEAN_NOT;
        $targetNamespace = implode(self::NAMESPACE_SEPARATOR, $targetNamespaces);

        return sprintf(
            self::FORMAT_TRANSFORM,
            $targetNamespace,
            self::NAMESPACE_SEPARATOR,
            $classWithoutNamespace
        );
    }
}
