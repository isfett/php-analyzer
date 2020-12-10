<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation;

/**
 * Class UnionType
 */
class UnionType extends AbstractRepresentation
{
    /** @var string */
    private const FORMAT_REPRESENTATION = '%s';

    /** @var string */
    private const UNION_SEPARATOR = '|';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\UnionType $node */
        $node = $this->node;

        return sprintf(
            self::FORMAT_REPRESENTATION,
            $this->arguments($node->types, self::UNION_SEPARATOR)
        );
    }
}
