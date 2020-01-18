<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Processor\MagicNumber;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\AbstractProcessor;
use PhpParser\Node;

/**
 * Class IgnoreZeroProcessor
 */
class IgnoreZeroProcessor extends AbstractProcessor
{
    /** @var float */
    private const ZERO_DOUBLE = 0.00;

    /** @var int */
    private const ZERO_INT = 0;

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
        if (self::ZERO_INT === $node->value || self::ZERO_DOUBLE === $node->value) {
            $this->nodeOccurrenceList->removeOccurrence($occurrence);
        }
    }
}
