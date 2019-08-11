<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Visitor\MagicNumber;

use Isfett\PhpAnalyzer\Node\AbstractVisitor;
use PhpParser\Node;

/**
 * Class PropertyVisitor
 */
class PropertyVisitor extends AbstractVisitor
{
    /**
     * @param Node $node
     *
     * @return int|null
     */
    public function enterNode(Node $node): ?int
    {
        if ($this->isNumber($node) && $this->isProperty($node)) {
            $this->addNodeOccurrence($node);
        }

        return null;
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    private function isProperty(Node $node): bool
    {
        /** @var Node $parent */
        $parentNode = $node->getAttribute('parent');

        return $parentNode instanceof Node\Stmt\PropertyProperty;
    }
}
