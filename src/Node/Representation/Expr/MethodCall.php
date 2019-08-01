<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class MethodCall
 */
class MethodCall extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\MethodCall $node */
        $node = $this->node;

        return sprintf(
            '%s->%s(%s)',
            $this->representate($node->var),
            $this->representate($node->name),
            $this->arguments($node->args)
        );
    }
}
