<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class New_
 */
class New_ extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function getRepresentation(): string
    {
        /** @var \PhpParser\Node\Expr\New_ $node */
        $node = $this->node;

        return sprintf(
            '(new %s(%s))',
            $this->representation($node->class),
            $this->arguments($node->args)
        );
    }
}
