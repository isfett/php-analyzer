<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr\Cast;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;
use PhpParser\Node;

/**
 * Class Double
 */
class Double extends AbstractRepresentation
{
    /** @var string */
    private const CAST_TYPE_DOUBLE = 'double';

    /** @var string */
    private const CAST_TYPE_FLOAT = 'float';

    /** @var string */
    private const FORMAT_REPRESENTATION = '(%s) %s';

    /** @var string */
    private const CAST_TYPE_REAL = 'real';

    /** @var string */
    private const NODE_ATTRIBUTE_CAST_TYPE = 'kind';

    /** @var array */
    private static $doubleTypes = [
        Node\Expr\Cast\Double::KIND_DOUBLE => self::CAST_TYPE_DOUBLE,
        Node\Expr\Cast\Double::KIND_FLOAT => self::CAST_TYPE_FLOAT,
        Node\Expr\Cast\Double::KIND_REAL => self::CAST_TYPE_REAL,
    ];

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\Cast\Double $node */
        $node = $this->node;

        return sprintf(
            self::FORMAT_REPRESENTATION,
            self::$doubleTypes[$node->getAttribute(self::NODE_ATTRIBUTE_CAST_TYPE)],
            $this->representate($node->expr)
        );
    }
}
