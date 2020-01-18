<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class BooleanNot
 */
class BooleanNot extends AbstractRepresentation
{
    /** @var string */
    private const FORMAT_REPRESENTATION = '%s%s';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\BooleanNot $node */
        $node = $this->node;

        return sprintf(
            self::FORMAT_REPRESENTATION,
            self::NEGATION_SIGN,
            $this->representate($node->expr)
        );
    }
}
