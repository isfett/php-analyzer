<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation;

/**
 * Class Param
 */
class Param extends AbstractRepresentation
{
    /** @var string */
    private const FORMAT_REPRESENTATION = '%s%s%s%s%s';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Param $node */
        $node = $this->node;

        $byRef = $node->byRef ? self::REF_SIGN : self::EMPTY_STRING;
        $type = $node->type ? $node->type . self::SPACE : self::EMPTY_STRING;
        $default = $node->default ?
            self::SPACE . self::EQUAL_SIGN . self::SPACE . $this->representate($node->default) :
            self::EMPTY_STRING;
        $variadic = $node->variadic ? self::VARIADIC_SIGN : self::EMPTY_STRING;

        return sprintf(
            self::FORMAT_REPRESENTATION,
            $type,
            $variadic,
            $byRef,
            $this->representate($node->var),
            $default
        );
    }
}
