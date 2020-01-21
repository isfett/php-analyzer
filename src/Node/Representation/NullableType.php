<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation;

/**
 * Class NullableType
 */
class NullableType extends AbstractRepresentation
{
    /** @var string */
    private const NULLABLE_OPERATOR = '?';

    /** @var string */
    private const REPRESENTATION_FORMAT = '%s%s';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\NullableType $node */
        $node = $this->node;

        return sprintf(self::REPRESENTATION_FORMAT, self::NULLABLE_OPERATOR, $this->representate($node->type));
    }
}
