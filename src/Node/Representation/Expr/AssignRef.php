<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class AssignRef
 */
class AssignRef extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\AssignRef $node */
        $node = $this->node;

        return sprintf(
            '%s =& %s',
            $this->representate($node->var),
            $this->representate($node->expr)
        );
    }
}
