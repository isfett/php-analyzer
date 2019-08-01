<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class ClassConstFetch
 */
class ClassConstFetch extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function getRepresentation(): string
    {
        /** @var \PhpParser\Node\Expr\ClassConstFetch $node */
        $node = $this->node;

        return sprintf(
            '%s::%s',
            $this->representation($node->class),
            $this->representation($node->name)
        );
    }
}