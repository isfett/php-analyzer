<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class Include_
 */
class Include_ extends AbstractRepresentation
{
    /** @var array */
    private static $includeFunctions = [
        \PhpParser\Node\Expr\Include_::TYPE_INCLUDE => 'include',
        \PhpParser\Node\Expr\Include_::TYPE_INCLUDE_ONCE => 'include_once',
        \PhpParser\Node\Expr\Include_::TYPE_REQUIRE => 'require',
        \PhpParser\Node\Expr\Include_::TYPE_REQUIRE_ONCE => 'require_once',
    ];

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\Include_ $node */
        $node = $this->node;

        return sprintf(
            '%s(%s)',
            self::$includeFunctions[$node->type],
            $this->representate($node->expr)
        );
    }
}
