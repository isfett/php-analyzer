<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Processor\MagicNumber;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\AbstractProcessor;
use PhpParser\Node;

/**
 * Class IgnoreMinusOneProcessor
 */
class IgnoreMinusOneProcessor extends AbstractProcessor
{
    /** @var float */
    private const ONE_DOUBLE = 1.00;

    /** @var int */
    private const ONE_INT = 1;

    /**
     * @param Occurrence $occurrence
     *
     * @return void
     */
    public function process(Occurrence $occurrence): void
    {
        /** @var Node\Scalar\LNumber|Node\Scalar\DNumber $node */
        $node = $occurrence->getNode();

        // phpcs:ignore SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
        if ($node->getAttribute(self::NODE_ATTRIBUTE_PARENT) instanceof Node\Expr\UnaryMinus && (
                self::ONE_INT === $node->value ||
                self::ONE_DOUBLE === $node->value
            )
        ) {
            $this->nodeOccurrenceList->removeOccurrence($occurrence);
        }
    }
}
