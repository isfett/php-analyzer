<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Processor\MagicString;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\AbstractProcessor;
use PhpParser\Node;

/**
 * Class IgnoreEmptyStringProcessor
 */
class IgnoreEmptyStringProcessor extends AbstractProcessor
{
    /**
     * @param Occurrence $occurrence
     *
     * @return void
     */
    public function process(Occurrence $occurrence): void
    {
        /** @var Node\Scalar\String_ $node */
        $node = $occurrence->getNode();

        if (self::EMPTY_STRING !== $node->value) {
            return;
        }

        $this->nodeOccurrenceList->removeOccurrence($occurrence);
    }
}
