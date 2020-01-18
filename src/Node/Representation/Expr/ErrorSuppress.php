<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class ErrorSuppress
 */
class ErrorSuppress extends AbstractRepresentation
{
    /** @var string */
    private const FORMAT_REPRESENTATION = '@%s';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\ErrorSuppress $node */
        $node = $this->node;

        return sprintf(
            self::FORMAT_REPRESENTATION,
            $this->representate($node->expr)
        );
    }
}
