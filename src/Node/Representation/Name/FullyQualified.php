<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Name;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class FullyQualified
 */
class FullyQualified extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Name\FullyQualified $node */
        $node = $this->node;

        return sprintf('\\%s', implode('\\', $node->parts));
    }
}
