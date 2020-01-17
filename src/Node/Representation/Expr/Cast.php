<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class Cast
 */
class Cast extends AbstractRepresentation
{
    /** @var int */
    private const FIRST_CHARACTER = 0;

    /** @var string */
    private const FORMAT_REPRESENTATION = '(%s) %s';

    /** @var int */
    private const NOT_LAST_CHARACTER = -1;

    /** @var int */
    private const SECOND_CHARACTER = 1;

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\Cast $node */
        $node = $this->node;

        $type = strtolower(substr(strrchr(get_class($node), self::NAMESPACE_SEPARATOR), self::SECOND_CHARACTER));
        if (self::UNDERSCORE === substr($type, self::NOT_LAST_CHARACTER)) {
            $type = substr($type, self::FIRST_CHARACTER, self::NOT_LAST_CHARACTER);
        }

        return sprintf(
            self::FORMAT_REPRESENTATION,
            $type,
            $this->representate($node->expr)
        );
    }
}
