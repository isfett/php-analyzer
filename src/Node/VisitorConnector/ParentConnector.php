<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\VisitorConnector;

use Isfett\PhpAnalyzer\Node\AbstractVisitor;
use PhpParser\Node;

/**
 * Class ParentConnector
 *
 * @see https://github.com/nikic/PHP-Parser/issues/238
 */
class ParentConnector extends AbstractVisitor
{
    /** @var int */
    private const ONE = 1;

    /** @var array */
    private $stack = [];

    /**
     * @param array $nodes
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beginTraverse(array $nodes): void
    {
        $this->stack = [];
    }

    /**
     * @param Node $node
     *
     * @return void
     */
    public function enterNode(Node $node): void
    {
        if (count($this->stack)) {
            /** @var Node $parent */
            $parent = $this->stack[count($this->stack) - self::ONE];
            $node->setAttribute(self::NODE_ATTRIBUTE_PARENT, $parent);
        }

        $this->stack[] = $node;
    }

    /**
     * @param Node $node
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function leaveNode(Node $node): void
    {
        array_pop($this->stack);
    }
}
