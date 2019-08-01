<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class PreInc
 */
class PreInc extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\PreInc $node */
        $node = $this->node;

        return sprintf(
            '++%s',
            $this->representate($node->var)
        );
    }
}
