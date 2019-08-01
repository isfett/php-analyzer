<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class PostDec
 */
class PostDec extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function getRepresentation(): string
    {
        /** @var \PhpParser\Node\Expr\PostDec $node */
        $node = $this->node;

        return sprintf(
            '%s--',
            $this->representation($node->var)
        );
    }
}
