<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Visitor\MagicString;

use Isfett\PhpAnalyzer\Node\AbstractVisitor;
use PhpParser\Node;

/**
 * Class DefaultParameterVisitor
 */
class DefaultParameterVisitor extends AbstractVisitor
{
    /**
     * @param Node $node
     *
     * @return int|null
     */
    public function enterNode(Node $node): ?int
    {
        if ($this->isParameterAndHasDefaultValueString($node)) {
            /** @var Node\Param $node */
            $this->addNodeOccurrence($node->default);
        }

        return null;
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    private function isParameterAndHasDefaultValueString(Node $node): bool
    {
        return $node instanceof Node\Param && null !== $node->default && $this->isString($node->default);
    }
}
