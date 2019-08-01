<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr\AssignOp;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class Concat
 */
class Concat extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\AssignOp\Concat $node */
        $node = $this->node;

        return sprintf(
            '%s .= %s',
            $this->representate($node->var),
            $this->representate($node->expr)
        );
    }
}
