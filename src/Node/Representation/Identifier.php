<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation;

/**
 * Class Identifier
 */
class Identifier extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Identifier $node */
        $node = $this->node;

        return $node->name;
    }
}
