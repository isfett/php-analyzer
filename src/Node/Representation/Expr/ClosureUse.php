<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class ClosureUse
 */
class ClosureUse extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\ClosureUse $node */
        $node = $this->node;

        return sprintf(
            '%s%s',
            $node->byRef ? '&' : '',
            $this->representate($node->var)
        );
    }
}
