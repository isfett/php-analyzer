<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class ArrayItem
 */
class ArrayItem extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\ArrayItem $node */
        $node = $this->node;

        $byRef = $node->byRef ? '&' : '';

        if (null !== $node->key) {
            return sprintf(
                '%s => %s%s',
                $this->representate($node->key),
                $byRef,
                $this->representate($node->value)
            );
        }

        $unpack = $node->unpack ? '...' : '';

        return sprintf(
            '%s%s%s',
            $unpack,
            $byRef,
            $this->representate($node->value)
        );
    }
}
