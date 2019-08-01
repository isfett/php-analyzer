<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class BitwiseNot
 */
class BitwiseNot extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\BitwiseNot $node */
        $node = $this->node;

        return sprintf(
            '%s%s',
            '~',
            $this->representate($node->expr)
        );
    }
}
