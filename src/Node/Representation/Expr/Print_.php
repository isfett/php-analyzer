<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class Print_
 */
class Print_ extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function getRepresentation(): string
    {
        /** @var \PhpParser\Node\Expr\Print_ $node */
        $node = $this->node;

        return sprintf(
            'print(%s)',
            $this->representation($node->expr)
        );
    }
}
