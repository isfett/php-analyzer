<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class ArrayItem
 */
class ArrayItem extends AbstractRepresentation
{
    /** @var string */
    private const FORMAT_REPRESENTATION = '%s%s%s';

    /** @var string */
    private const FORMAT_REPRESENTATION_KEY = '%s => %s%s';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\ArrayItem $node */
        $node = $this->node;

        $byRef = $node->byRef ? self::REF_SIGN : self::EMPTY_STRING;

        if (null !== $node->key) {
            return sprintf(
                self::FORMAT_REPRESENTATION_KEY,
                $this->representate($node->key),
                $byRef,
                $this->representate($node->value)
            );
        }

        $unpack = $node->unpack ? self::VARIADIC_SIGN : self::EMPTY_STRING;

        return sprintf(
            self::FORMAT_REPRESENTATION,
            $unpack,
            $byRef,
            $this->representate($node->value)
        );
    }
}
