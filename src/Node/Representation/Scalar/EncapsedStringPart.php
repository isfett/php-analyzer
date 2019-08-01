<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Scalar;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class EncapsedStringPart
 */
class EncapsedStringPart extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function getRepresentation(): string
    {
        /** @var \PhpParser\Node\Scalar\EncapsedStringPart $node */
        $node = $this->node;

        return $node->value;
    }
}
