<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class UnaryPlus
 */
class UnaryPlus extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function getRepresentation(): string
    {
        /** @var \PhpParser\Node\Expr\UnaryPlus $node */
        $node = $this->node;

        return sprintf(
            '+%s',
            $this->representation($node->expr)
        );
    }
}
