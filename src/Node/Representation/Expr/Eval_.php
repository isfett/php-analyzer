<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class Eval_
 */
class Eval_ extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\Eval_ $node */
        $node = $this->node;

        return sprintf(
            'eval(%s)',
            $this->representate($node->expr)
        );
    }
}
