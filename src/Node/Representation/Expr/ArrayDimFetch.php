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
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\ArrayDimFetch $node */
        $node = $this->node;


        return sprintf(
            '%s[%s]',
            $this->representate($node->var),
            null === $node->dim ? '' : $this->representate($node->dim)
        );
    }
}
