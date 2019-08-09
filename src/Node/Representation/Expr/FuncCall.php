<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class FuncCall
 */
class FuncCall extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\FuncCall $node */
        $node = $this->node;

        return sprintf(
            '%s(%s)',
            $this->representate($node->name),
            $this->arguments($node->args)
        );
    }
}
