<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class List_
 */
class List_ extends AbstractRepresentation
{
    /** @var string */
    private const FORMAT_REPRESENTATION = 'list(%s)';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\List_ $node */
        $node = $this->node;

        return sprintf(
            self::FORMAT_REPRESENTATION,
            $this->arguments($node->items)
        );
    }
}
