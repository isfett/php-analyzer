<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation;

/**
 * Class Arg
 */
class Arg extends AbstractRepresentation
{
    /** @var string */
    private const FORMAT_REPRESENTATION = '%s%s%s';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Arg $node */
        $node = $this->node;

        $byRef = $node->byRef ? self::REF_SIGN : self::EMPTY_STRING;

        $unpack = $node->unpack ? self::VARIADIC_SIGN : self::EMPTY_STRING;

        return sprintf(
            self::FORMAT_REPRESENTATION,
            $unpack,
            $byRef,
            $this->representate($node->value)
        );
    }
}
