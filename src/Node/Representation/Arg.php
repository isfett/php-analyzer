<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation;

/**
 * Class Arg
 */
class Arg extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function getRepresentation(): string
    {
        /** @var \PhpParser\Node\Arg $node */
        $node = $this->node;

        $byRef = $node->byRef ? '&' : '';

        $unpack = $node->unpack ? '...' : '';

        return sprintf(
            '%s%s%s',
            $unpack,
            $byRef,
            $this->representation($node->value)
        );
    }
}
