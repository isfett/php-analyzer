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
    /** @var array */
    private $stack;

    /**
     * @param array $nodes
     *
     * @return void
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
        if (!empty($this->stack)) {
            /** @var Node $parent */
            $parent = $this->stack[count($this->stack)-1];
            $node->setAttribute('parent', $parent);
        }
        $this->stack[] = $node;
    }

    /**
     * @param Node $node
     *
     * @return void
     */
    public function leaveNode(Node $node): void
    {
        array_pop($this->stack);
    }
}
