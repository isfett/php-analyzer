<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class ClosureUse
 */
class ClosureUse extends AbstractRepresentation
{
    /** @var string */
    private const FORMAT_REPRESENTATION = '%s%s';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\ClosureUse $node */
        $node = $this->node;

        return sprintf(
            self::FORMAT_REPRESENTATION,
            $node->byRef ? self::REF_SIGN : self::EMPTY_STRING,
            $this->representate($node->var)
        );
    }
}
