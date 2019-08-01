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
    public function getRepresentation(): string
    {
        /** @var \PhpParser\Node\Expr\ArrayItem $node */
        $node = $this->node;

        $byRef = $node->byRef ? '&' : '';

        if (null !== $node->key) {
            return sprintf(
                '%s => %s%s',
                $this->representation($node->key),
                $byRef,
                $this->representation($node->value)
            );
        }

        $unpack = $node->unpack ? '...' : '';

        return sprintf(
            '%s%s%s',
            $unpack,
            $byRef,
            $this->representation($node->value)
        );
    }
}
