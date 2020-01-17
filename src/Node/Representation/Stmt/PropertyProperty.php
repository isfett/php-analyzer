<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Stmt;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class PropertyProperty
 */
class PropertyProperty extends AbstractRepresentation
{
    /** @var string */
    private const FORMAT_REPRESENTATION = '%s = %s';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Stmt\PropertyProperty $node */
        $node = $this->node;

        if (null === $node->default) {
            return $this->representate($node->name);
        }

        return sprintf(
            self::FORMAT_REPRESENTATION,
            $this->representate($node->name),
            $this->representate($node->default)
        );
    }
}
