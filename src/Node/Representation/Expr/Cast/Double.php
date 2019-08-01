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
    private static $doubleTypes = [
        \PhpParser\Node\Expr\Cast\Double::KIND_DOUBLE => 'double',
        \PhpParser\Node\Expr\Cast\Double::KIND_FLOAT => 'float',
        \PhpParser\Node\Expr\Cast\Double::KIND_REAL => 'real',
    ];

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\Cast\Double $node */
        $node = $this->node;

        return sprintf(
            '(%s) %s',
            self::$doubleTypes[$node->getAttribute('kind')],
            $this->representate($node->expr)
        );
    }
}
