<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class StaticCall
 */
class StaticCall extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function getRepresentation(): string
    {
        /** @var \PhpParser\Node\Expr\StaticCall $node */
        $node = $this->node;

        return sprintf(
            '%s::%s(%s)',
            $this->representation($node->class),
            $this->representation($node->name),
            $this->arguments($node->args)
        );
    }
}
