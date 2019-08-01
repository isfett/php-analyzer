<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Processor;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\AbstractProcessor;
use PhpParser\Node;

/**
 * Class RemoveSingleFullyQualifiedNamesProcessor
 */
class RemoveSingleFullyQualifiedNamesProcessor extends AbstractProcessor
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
        //print_r($node);
        if ($node instanceof Node\Expr\BinaryOp) {
            $node = $this->replaceFullyQualifiedNameInBinaryOp($node);
        } else {
            $node = $this->replaceFullyQualifiedName($node);
        }
        $occurrence->setNode($node);
    }

    /**
     * @param Node $node
     *
     * @return Node
     */
    private function replaceFullyQualifiedName(Node $node): Node
    {
        if (property_exists($node, 'name') &&
            $node->name instanceof Node\Name\FullyQualified &&
            1 === count($node->name->parts)
        ) {
            $node->name = $this->generateNameNodeFromFullyQualified($node->name);
        }

        return $node;
    }

    /**
     * @param Node\Expr\BinaryOp $node
     *
     * @return void
     */
    private function replaceFullyQualifiedNameInBinaryOp(Node\Expr\BinaryOp $node): Node\Expr\BinaryOp
    {
        foreach (['left', 'right'] as $binaryOpSide) {
            if ($node->$binaryOpSide instanceof Node\Expr\BinaryOp) {
                $node->$binaryOpSide = $this->replaceFullyQualifiedNameInBinaryOp($node->$binaryOpSide);
            }
            $node->$binaryOpSide = $this->replaceFullyQualifiedName($node->$binaryOpSide);
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
