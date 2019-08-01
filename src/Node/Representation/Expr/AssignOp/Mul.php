<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr\AssignOp;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class Mul
 */
class Mul extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\AssignOp\Mul $node */
        $node = $this->node;

        return sprintf(
            '%s *= %s',
            $this->representate($node->var),
            $this->representate($node->expr)
        );
    }
}
