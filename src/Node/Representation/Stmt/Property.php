<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Stmt;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;
use PhpParser\Node\Stmt\Property as NodeProperty;

/**
 * Class Property
 */
class Property extends AbstractRepresentation
{
    /** @var string */
    private const FLAG_PRIVATE = 'private';

    /** @var string */
    private const FLAG_PROTECTED = 'protected';

    /** @var string */
    private const FLAG_PUBLIC = 'public';

    /** @var string */
    private const FLAG_STATIC = 'static';

    /** @var string */
    private const FORMAT_REPRESENTATION = '%s%s%s%s';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var NodeProperty $node */
        $node = $this->node;

        $accessModifier = $this->getAccessModifierRepresentation($node);
        $static = $this->getStaticRepresentation($node);

        return sprintf(
            self::FORMAT_REPRESENTATION,
            null === $accessModifier ? self::EMPTY_STRING : $accessModifier.self::SPACE,
            null === $static ? self::EMPTY_STRING : $static.self::SPACE,
            null === $node->type ? self::EMPTY_STRING : $this->representate($node->type).self::SPACE,
            $this->arguments($node->props)
        );
    }

    /**
     * @param NodeProperty $node
     *
     * @return string|null
     */
    private function getAccessModifierRepresentation(NodeProperty $node): ?string
    {
        if ($node->isPrivate()) {
            return self::FLAG_PRIVATE;
        }

        if ($node->isProtected()) {
            return self::FLAG_PROTECTED;
        }

        return self::FLAG_PUBLIC; // default
    }

    /**
     * @param NodeProperty $node
     *
     * @return string|null
     */
    private function getStaticRepresentation(NodeProperty $node): ?string
    {
        if ($node->isStatic()) {
            return self::FLAG_STATIC;
        }

        return null;
    }
}
