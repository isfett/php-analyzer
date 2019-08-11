<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Stmt;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class PropertyProperty
 */
class PropertyProperty extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Stmt\PropertyProperty $node */
        $node = $this->node;

        if (null === $node->default) {
            return sprintf('%s', $this->representate($node->name));
        }

        return sprintf(
            '%s = %s',
            $this->representate($node->name),
            $this->representate($node->default)
        );
    }
}
