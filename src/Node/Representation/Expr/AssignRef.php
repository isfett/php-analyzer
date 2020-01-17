<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class AssignRef
 */
class AssignRef extends AbstractRepresentation
{
    /** @var string */
    private const FORMAT_REPRESENTATION = '%s %s%s %s';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\AssignRef $node */
        $node = $this->node;

        return sprintf(
            self::FORMAT_REPRESENTATION,
            $this->representate($node->var),
            self::EQUAL_SIGN,
            self::REF_SIGN,
            $this->representate($node->expr)
        );
    }
}
