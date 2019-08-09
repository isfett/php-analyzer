<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class Variable
 */
class Variable extends AbstractRepresentation
{
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
            '%s%s',
            '$',
            $name
        );
    }
}
