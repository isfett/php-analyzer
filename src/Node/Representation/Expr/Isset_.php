<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class Isset_
 */
class Isset_ extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\Isset_ $node */
        $node = $this->node;

        return sprintf(
            'isset(%s)',
            $this->arguments($node->vars)
        );
    }
}
