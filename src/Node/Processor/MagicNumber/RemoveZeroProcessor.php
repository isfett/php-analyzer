<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Processor\MagicNumber;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\AbstractProcessor;
use PhpParser\Node;

/**
 * Class RemoveZeroProcessor
 */
class RemoveZeroProcessor extends AbstractProcessor
{
    /**
     * @param Occurrence $occurrence
     *
     * @return void
     */
    public function process(Occurrence $occurrence): void
    {
        /** @var Node\Scalar\LNumber|Node\Scalar\DNumber $node */
        $node = $occurrence->getNode();

        if (0 === $node->value || 0.00 === $node->value) {
            $this->nodeOccurrenceList->removeOccurrence($occurrence);
        }
    }
}
