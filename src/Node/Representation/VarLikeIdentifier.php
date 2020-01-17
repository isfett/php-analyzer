<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation;

/**
 * Class VarLikeIdentifier
 */
class VarLikeIdentifier extends AbstractRepresentation
{
    /** @var string */
    private const FORMAT_REPRESENTATION = '%s%s';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\VarLikeIdentifier $node */
        $node = $this->node;

        return sprintf(
            self::FORMAT_REPRESENTATION,
            self::VARIABLE_SIGN,
            $node->name
        );
    }
}
