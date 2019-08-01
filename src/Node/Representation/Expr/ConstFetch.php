<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class ConstFetch
 */
class ConstFetch extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function getRepresentation(): string
    {
        /** @var \PhpParser\Node\Expr\ConstFetch $node */
        $node = $this->node;

        return $this->representation($node->name);
    }
}
