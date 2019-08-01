<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Scalar;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class Encapsed
 */
class Encapsed extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Scalar\Encapsed $node */
        $node = $this->node;

        return sprintf('"%s"', $this->arguments($node->parts, ''));
    }
}
