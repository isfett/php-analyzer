<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Service\SortService;

use Doctrine\Common\Collections\Criteria;
use Isfett\PhpAnalyzer\DAO\Configuration\Sort;
use Isfett\PhpAnalyzer\DAO\Configuration\SortField;
use Isfett\PhpAnalyzer\DAO\Occurrence;
use PhpParser\Node;

/**
 * Class SortByNodeValues
 */
class SortByNodeValues
{
    /** @var string */
    private const NODE_ATTRIBUTE_PARENT = 'parent';

    /** @var int */
    private const RESULT_DRAW = 0;

    /** @var int */
    private const RESULT_NEGATE = -1;

    /** @var string */
    private const SORT_FIELD_VALUE = 'value';

    /** @var string */
    public const SORT_FUNCTION = 'sort';

    /** @var Sort */
    private $sortConfiguration;

    /**
     * SortByNodeValues constructor.
     *
     * @param Sort $sortConfiguration
     */
    public function __construct(Sort $sortConfiguration)
    {
        $this->sortConfiguration = $sortConfiguration;
    }

    /**
     * @param Occurrence $occurrenceA
     * @param Occurrence $occurrenceB
     *
     * @return int
     */
    public function sort(Occurrence $occurrenceA, Occurrence $occurrenceB): int
    {
        /** @var Node\Scalar\LNumber|Node\Scalar\DNumber $nodeA */
        /** @var Node\Scalar\LNumber|Node\Scalar\DNumber $nodeB */
        $nodeA = $occurrenceA->getNode();
        $nodeB = $occurrenceB->getNode();

        $valueA = $this->getNodeValue($nodeA);
        $valueB = $this->getNodeValue($nodeB);

        $result = self::RESULT_DRAW;

        /** @var SortField $sortField */
        foreach ($this->sortConfiguration->getFields() as $sortField) {
            $field = $sortField->getField();
            $result = strtolower((string) $nodeA->$field) <=> strtolower((string) $nodeB->$field);
            if (self::SORT_FIELD_VALUE === $field) {
                $result = $valueA <=> $valueB;
            }

            if (Criteria::DESC === $sortField->getDirection()) {
                $result *= self::RESULT_NEGATE;
            }

            if (self::RESULT_DRAW !== $result) {
                return $result;
            }
        }

        return $result;
    }

    /**
     * @param Node $node
     *
     * @return string
     */
    private function getNodeValue(Node $node): string
    {
        $value = strtolower((string) $node->value);
        if ($node->hasAttribute(self::NODE_ATTRIBUTE_PARENT) &&
            $node->getAttribute(self::NODE_ATTRIBUTE_PARENT) instanceof Node\Expr\UnaryMinus
        ) {
            $value *= self::RESULT_NEGATE;
        }

        return (string) $value;
    }
}
