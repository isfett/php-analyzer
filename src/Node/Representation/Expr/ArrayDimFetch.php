<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class ArrayDimFetch
 */
class ArrayDimFetch extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function getRepresentation(): string
    {
        /** @var \PhpParser\Node\Expr\ArrayDimFetch $node */
        $node = $this->node;


        return sprintf(
            '%s[%s]',
            $this->representation($node->var),
            null === $node->dim ? '' : $this->representation($node->dim)
        );
    }
}
