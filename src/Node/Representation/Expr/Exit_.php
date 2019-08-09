<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class Exit_
 */
class Exit_ extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\Exit_ $node */
        $node = $this->node;

        if (null === $node->expr) {
            return 'exit';
        }

        return sprintf(
            'exit(%s)',
            $this->representate($node->expr)
        );
    }
}
