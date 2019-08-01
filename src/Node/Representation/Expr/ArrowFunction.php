<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class ArrowFunction
 */
class ArrowFunction extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function getRepresentation(): string
    {
        /** @var \PhpParser\Node\Expr\ArrowFunction $node */
        $node = $this->node;

        $static = '';
        if ($node->static) {
            $static = 'static ';
        }

        $returnType = '';
        if (null !== $node->returnType) {
            $returnType = sprintf(': %s', $this->representation($node->returnType));
        }

        return sprintf(
            '(%sfn%s(%s)%s => %s)',
            $static,
            $node->byRef ? '&' : '',
            $this->arguments($node->params),
            $returnType,
            $this->representation($node->expr)
        );
    }
}
