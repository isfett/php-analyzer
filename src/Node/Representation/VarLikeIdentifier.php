<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation;

/**
 * Class VarLikeIdentifier
 */
class VarLikeIdentifier extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\VarLikeIdentifier $node */
        $node = $this->node;

        return sprintf(
            '%s%s',
            '$',
            $node->name
        );
    }
}
