<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Visitor\MagicNumber;

use Isfett\PhpAnalyzer\Node\AbstractVisitor;
use PhpParser\Node;

/**
 * Class ArrayVisitor
 */
class ArrayVisitor extends AbstractVisitor
{
    /**
     * @param Node $node
     *
     * @return int|null
     */
    public function enterNode(Node $node): ?int
    {
        if ($this->isNumber($node) && $this->isArray($node)) {
            $this->addNodeOccurrence($node);
        }

        return null;
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    private function isArray(Node $node): bool
    {
        /** @var Node $parent */
        $parentNode = $node->getAttribute(self::NODE_ATTRIBUTE_PARENT);

        if ($parentNode instanceof Node\Scalar\LNumber || $parentNode instanceof Node\Scalar\DNumber) {
            $parentNode = $parentNode->getAttribute(self::NODE_ATTRIBUTE_PARENT);
        }

        return $parentNode instanceof Node\Expr\ArrayDimFetch || $parentNode instanceof Node\Expr\ArrayItem;
    }
}
