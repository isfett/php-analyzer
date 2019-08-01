<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class Closure
 */
class Closure extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function getRepresentation(): string
    {
        /** @var \PhpParser\Node\Expr\Closure $node */
        $node = $this->node;

        $use = '';
        if (count($node->uses)) {
            $use = sprintf(
                'use (%s) ',
                $this->arguments($node->uses)
            );
        }

        $static = '';
        if ($node->static) {
            $static = 'static ';
        }

        return sprintf(
            '%sfunction%s(%s) %s{ /* CLOSURE */ }',
            $static,
            $node->byRef ? '&' : '',
            $this->arguments($node->params),
            $use
        );
    }
}
