<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Processor\Condition;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\AbstractProcessor;
use PhpParser\Node;

/**
 * Class RemoveSingleFullyQualifiedNameProcessor
 */
class RemoveSingleFullyQualifiedNameProcessor extends AbstractProcessor
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
            $node = $this->replaceFullyQualifiedNameInBinaryOp($node, $occurrence);
        } else {
            $node = $this->replaceFullyQualifiedName($node, $occurrence);
        }
        $occurrence->setNode($node);
    }

    /**
     * @param Node       $node
     * @param Occurrence $occurrence
     *
     * @return Node
     */
    private function replaceFullyQualifiedName(Node $node, Occurrence $occurrence): Node
    {
        /** @var Node\Expr\FuncCall|Node\Expr\ConstFetch $node */
        if (property_exists($node, 'name') &&
            $node->name instanceof Node\Name\FullyQualified &&
            1 === count($node->name->parts)
        ) {
            /** @var Node\Name\FullyQualified $name */
            $name = $node->name;
            $node->name = $this->generateNameNodeFromFullyQualified($name);
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
    private function replaceFullyQualifiedNameInBinaryOp(
        Node\Expr\BinaryOp $node,
        Occurrence $occurrence
    ): Node\Expr\BinaryOp {
        foreach (['left', 'right'] as $binaryOpSide) {
            if ($node->$binaryOpSide instanceof Node\Expr\BinaryOp) {
                $node->$binaryOpSide = $this->replaceFullyQualifiedNameInBinaryOp($node->$binaryOpSide, $occurrence);
            }
            $node->$binaryOpSide = $this->replaceFullyQualifiedName($node->$binaryOpSide, $occurrence);
        }

        return $node;
    }

    /**
     * @param Node\Name\FullyQualified $name
     *
     * @return Node\Name
     */
    private function generateNameNodeFromFullyQualified(Node\Name\FullyQualified $name): Node\Name
    {
        return new Node\Name(
            end($name->parts),
            $name->getAttributes()
        );
    }
}
