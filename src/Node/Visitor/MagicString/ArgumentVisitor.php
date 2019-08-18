<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Visitor\MagicString;

use Isfett\PhpAnalyzer\Node\AbstractVisitor;
use PhpParser\Node;

/**
 * Class ArgumentVisitor
 */
class ArgumentVisitor extends AbstractVisitor
{
    /**
     * @param Node $node
     *
     * @return int|null
     */
    public function enterNode(Node $node): ?int
    {
        if ($this->isString($node) && $this->isArgument($node)) {
            $this->addNodeOccurrence($node);
        }

        return null;
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    private function isArgument(Node $node): bool
    {
        /** @var Node $parent */
        $parentNode = $node->getAttribute('parent');

        return $parentNode instanceof Node\Arg;
    }
}
