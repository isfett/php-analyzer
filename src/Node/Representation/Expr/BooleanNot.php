<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class BooleanNot
 */
class BooleanNot extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function getRepresentation(): string
    {
        /** @var \PhpParser\Node\Expr\BooleanNot $node */
        $node = $this->node;

        return sprintf(
            '%s%s',
            '!',
            $this->representation($node->expr)
        );
    }
}
