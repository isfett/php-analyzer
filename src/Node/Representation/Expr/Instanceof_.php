<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class Instanceof_
 */
class Instanceof_ extends AbstractRepresentation
{
    /** @var string */
    private const FORMAT_REPRESENTATION = '%s instanceof %s';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\Instanceof_ $node */
        $node = $this->node;

        return sprintf(
            self::FORMAT_REPRESENTATION,
            $this->representate($node->expr),
            $this->representate($node->class)
        );
    }
}
