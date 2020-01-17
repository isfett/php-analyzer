<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;
use PhpParser\Node;

/**
 * Class Include_
 */
class Include_ extends AbstractRepresentation
{
    /** @var string */
    private const FORMAT_REPRESENTATION = '%s(%s)';

    /** @var string */
    private const TYPE_INCLUDE = 'include';

    /** @var string */
    private const TYPE_INCLUDE_ONCE = 'include_once';

    /** @var string */
    private const TYPE_REQUIRE = 'require';

    /** @var string */
    private const TYPE_REQUIRE_ONCE = 'require_once';

    /** @var array */
    private static $includeFunctions = [
        Node\Expr\Include_::TYPE_INCLUDE => self::TYPE_INCLUDE,
        Node\Expr\Include_::TYPE_REQUIRE => self::TYPE_REQUIRE,
        Node\Expr\Include_::TYPE_INCLUDE_ONCE => self::TYPE_INCLUDE_ONCE,
        Node\Expr\Include_::TYPE_REQUIRE_ONCE => self::TYPE_REQUIRE_ONCE,
    ];

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\Include_ $node */
        $node = $this->node;

        return sprintf(
            self::FORMAT_REPRESENTATION,
            self::$includeFunctions[$node->type],
            $this->representate($node->expr)
        );
    }
}
