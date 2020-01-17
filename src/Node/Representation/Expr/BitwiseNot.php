<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class BitwiseNot
 */
class BitwiseNot extends AbstractRepresentation
{
    /** @var string */
    private const FORMAT_REPRESENTATION = '%s%s';

    /** @var string */
    private const OPERATOR = '~';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\BitwiseNot $node */
        $node = $this->node;

        return sprintf(
            self::FORMAT_REPRESENTATION,
            self::OPERATOR,
            $this->representate($node->expr)
        );
    }
}
