<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Visitor\MagicString;

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
        if ($this->isString($node) && $this->isArray($node)) {
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

        return $parentNode instanceof Node\Expr\ArrayDimFetch || $parentNode instanceof Node\Expr\ArrayItem;
    }
}
