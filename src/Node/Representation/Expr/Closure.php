<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class Closure
 */
class Closure extends AbstractRepresentation
{
    /** @var string */
    private const FORMAT_REPRESENTATION = '%sfunction%s(%s) %s{ /* CLOSURE */ }';

    /** @var string */
    private const FORMAT_STATIC = 'static ';

    /** @var string */
    private const FORMAT_USE = 'use (%s) ';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\Closure $node */
        $node = $this->node;

        $use = self::EMPTY_STRING;
        if (count($node->uses)) {
            $use = sprintf(
                self::FORMAT_USE,
                $this->arguments($node->uses)
            );
        }

        $static = self::EMPTY_STRING;
        if ($node->static) {
            $static = self::FORMAT_STATIC;
        }

        return sprintf(
            self::FORMAT_REPRESENTATION,
            $static,
            $node->byRef ? self::REF_SIGN : self::EMPTY_STRING,
            $this->arguments($node->params),
            $use
        );
    }
}
