<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation;

/**
 * Class Name
 */
class Name extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Name $node */
        $node = $this->node;

        return implode('\\', $node->parts);
    }
}
