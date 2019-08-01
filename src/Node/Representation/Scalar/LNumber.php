<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Scalar;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class LNumber
 */
class LNumber extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function getRepresentation(): string
    {
        /** @var \PhpParser\Node\Scalar\LNumber $node */
        $node = $this->node;

        return (string) $node->value;
    }
}
