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
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\StaticPropertyFetch $node */
        $node = $this->node;

        return sprintf(
            '%s::%s',
            $this->representate($node->class),
            $this->representate($node->name)
        );
    }
}
