<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class Ternary
 */
class Ternary extends AbstractRepresentation
{
    /** @var string */
    private const FORMAT_REPRESENTATION = '%s ?%s%s%s: %s';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\Ternary $node */
        $node = $this->node;

        return sprintf(
            self::FORMAT_REPRESENTATION,
            $this->representate($node->cond),
            null === $node->if ? self::EMPTY_STRING : self::SPACE,
            $this->representate($node->if),
            null === $node->if ? self::EMPTY_STRING : self::SPACE,
            $this->representate($node->else)
        );
    }
}
