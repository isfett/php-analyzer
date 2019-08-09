<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class Clone_
 */
class Clone_ extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\Clone_ $node */
        $node = $this->node;

        return sprintf(
            'clone %s',
            $this->representate($node->expr)
        );
    }
}
