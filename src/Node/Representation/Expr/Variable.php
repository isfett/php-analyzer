<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class Variable
 */
class Variable extends AbstractRepresentation
{
    /** @var string */
    private const FORMAT_REPRESENTATION = '%s%s';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\Variable $node */
        $node = $this->node;

        $name = $node->name;

        if (!is_string($name)) {
            $name = $this->representate($name);
        }

        return sprintf(
            self::FORMAT_REPRESENTATION,
            self::VARIABLE_SIGN,
            $name
        );
    }
}
