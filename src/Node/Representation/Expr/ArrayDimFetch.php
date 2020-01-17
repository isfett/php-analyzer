<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class ArrayDimFetch
 */
class ArrayDimFetch extends AbstractRepresentation
{
    /** @var string */
    private const FORMAT_REPRESENTATION = '%s[%s]';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\ArrayDimFetch $node */
        $node = $this->node;


        return sprintf(
            self::FORMAT_REPRESENTATION,
            $this->representate($node->var),
            null === $node->dim ? '' : $this->representate($node->dim)
        );
    }
}
