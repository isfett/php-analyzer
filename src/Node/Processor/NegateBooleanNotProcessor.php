<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Processor;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\AbstractProcessor;
use Isfett\PhpAnalyzer\Node\Expr\NegationInterface;
use PhpParser\Node;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\BinaryOp;

/**
 * Class NegateBooleanNotProcessor
 */
class NegateBooleanNotProcessor extends AbstractProcessor
{
    /**
     * @param Occurrence $occurrence
     *
     * @return void
     */
    public function process(Occurrence $occurrence): void
    {
        $node = $occurrence->getNode();
        if ($this->isBooleanNotAndHasBinaryOpExpression($node)) {
            /** @var Node\Expr\BooleanNot $node */
            /** @var BinaryOp $expression */
            $expression = $node->expr;

            $negatedNode = $this->processBinaryOp($expression);
            if (null !== $negatedNode) {
                $occurrence->setNode($negatedNode);
            }
        }
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    private function isBooleanNotAndHasBinaryOpExpression(Node $node): bool
    {
        return $node instanceof BooleanNot && $node->expr instanceof BinaryOp;
    }

    /**
     * @param string $classname
     *
     * @return string
     */
    private function transformClassname(string $classname): string
    {
        $classWithNamespaces = explode('\\', $classname);
        $classWithoutNamespace = end($classWithNamespaces);

        $targetNamespaces = explode('\\', NegationInterface::class);
        array_pop($targetNamespaces);
        $targetNamespaces[] = 'BooleanNot';
        $targetNamespace = implode('\\', $targetNamespaces);

        return sprintf(
            '%s\\%s',
            $targetNamespace,
            $classWithoutNamespace
        );
    }

    /**
     * @param BinaryOp $node
     *
     * @return BinaryOp
     */
    private function processBinaryOp(BinaryOp $node): BinaryOp
    {
        $negationClassname = $this->transformClassname(get_class($node));

        if (class_exists($negationClassname)) {
            /** @var NegationInterface $negationClass */
            $negationClass = new $negationClassname();

            return $negationClass->negate($node);
        }

        $node = $this->processLogicalOp($node);

        return $node;
    }

    /**
     * @param BinaryOp $node
     *
     * @return BinaryOp
     */
    private function processLogicalOp(BinaryOp $node): BinaryOp
    {
        foreach (['left', 'right'] as $binaryOpSide) {
            if ($this->isBooleanNotAndHasBinaryOpExpression($node->$binaryOpSide)) {
                $node->$binaryOpSide = $this->processBinaryOp($node->$binaryOpSide->expr);
            }

            if ($node->$binaryOpSide instanceof BinaryOp) {
                $node->$binaryOpSide = $this->processBinaryOp($node->$binaryOpSide);
            }
        }

        return $node;
    }
}
