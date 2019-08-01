<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class Ternary
 */
class Ternary extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function getRepresentation(): string
    {
        /** @var \PhpParser\Node\Expr\Ternary $node */
        $node = $this->node;

        return sprintf(
            '%s',
            $this->representation($node->cond)
        );
    }
}
