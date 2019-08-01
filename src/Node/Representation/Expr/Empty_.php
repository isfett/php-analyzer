<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class Empty_
 */
class Empty_ extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function getRepresentation(): string
    {
        /** @var \PhpParser\Node\Expr\Empty_ $node */
        $node = $this->node;

        return sprintf(
            'empty(%s)',
            $this->representation($node->expr)
        );
    }
}
