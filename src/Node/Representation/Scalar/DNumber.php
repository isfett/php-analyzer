<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Scalar;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class DNumber
 */
class DNumber extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Scalar\DNumber $node */
        $node = $this->node;

        return (string) $node->value;
    }
}
