<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class StaticCall
 */
class StaticCall extends AbstractRepresentation
{
    /** @var string */
    private const FORMAT_REPRESENTATION = '%s::%s(%s)';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\StaticCall $node */
        $node = $this->node;

        return sprintf(
            self::FORMAT_REPRESENTATION,
            $this->representate($node->class),
            $this->representate($node->name),
            $this->arguments($node->args)
        );
    }
}
