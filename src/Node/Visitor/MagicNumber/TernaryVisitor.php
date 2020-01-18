<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Visitor\MagicNumber;

use Isfett\PhpAnalyzer\Node\AbstractVisitor;
use PhpParser\Node;

/**
 * Class TernaryVisitor
 */
class TernaryVisitor extends AbstractVisitor
{
    /** @var array */
    private const TERNARY_SIDES = [self::SIDE_IF, self::SIDE_ELSE];

    /** @var string */
    private const SIDE_ELSE = 'else';

    /** @var string */
    private const SIDE_IF = 'if';

    /**
     * @param Node $node
     *
     * @return int|null
     */
    public function enterNode(Node $node): ?int
    {
        if (!$node instanceof Node\Expr\Ternary) {
            return null;
        }

        foreach (self::TERNARY_SIDES as $side) {
            if (null === $node->$side) {
                continue;
            }

            if (!$this->isNumber($node->$side)) {
                continue;
            }

            $this->addNodeOccurrence($node->$side);
        }

        return null;
    }
}
