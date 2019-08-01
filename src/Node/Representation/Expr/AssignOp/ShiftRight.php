<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr\AssignOp;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class ShiftRight
 */
class ShiftRight extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function getRepresentation(): string
    {
        /** @var \PhpParser\Node\Expr\AssignOp\ShiftRight $node */
        $node = $this->node;

        return sprintf(
            '%s >>= %s',
            $this->representation($node->var),
            $this->representation($node->expr)
        );
    }
}