<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Name;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class FullyQualified
 */
class FullyQualified extends AbstractRepresentation
{
    /** @var string */
    private const FORMAT_REPRESENTATION = '%s%s';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Name\FullyQualified $node */
        $node = $this->node;

        return sprintf(
            self::FORMAT_REPRESENTATION,
            self::NAMESPACE_SEPARATOR,
            implode(self::NAMESPACE_SEPARATOR, $node->parts)
        );
    }
}
