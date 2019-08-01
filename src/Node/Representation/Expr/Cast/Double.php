<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr\Cast;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class Double
 */
class Double extends AbstractRepresentation
{
    /** @var array */
    private $doubleTypes = [
        \PhpParser\Node\Expr\Cast\Double::KIND_DOUBLE => 'double',
        \PhpParser\Node\Expr\Cast\Double::KIND_FLOAT => 'float',
        \PhpParser\Node\Expr\Cast\Double::KIND_REAL => 'real',
    ];

    /**
     * @return string
     */
    public function getRepresentation(): string
    {
        /** @var \PhpParser\Node\Expr\Cast\Double $node */
        $node = $this->node;

        return sprintf(
            '(%s) %s',
            $this->doubleTypes[$node->getAttribute('kind')],
            $this->representation($node->expr)
        );
    }
}
