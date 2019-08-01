<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class StaticPropertyFetch
 */
class StaticPropertyFetch extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function getRepresentation(): string
    {
        /** @var \PhpParser\Node\Expr\StaticPropertyFetch $node */
        $node = $this->node;

        return sprintf(
            '%s::%s',
            $this->representation($node->class),
            $this->representation($node->name)
        );
    }
}
