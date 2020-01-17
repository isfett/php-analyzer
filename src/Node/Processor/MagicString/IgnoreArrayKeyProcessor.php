<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Processor\MagicString;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\AbstractProcessor;
use PhpParser\Node;

/**
 * Class IgnoreArrayKeyProcessor
 */
class IgnoreArrayKeyProcessor extends AbstractProcessor
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

        if (!$node->getAttribute(self::NODE_ATTRIBUTE_PARENT) instanceof Node\Expr\ArrayItem) {
            return;
        }

        $key = $node->getAttribute(self::NODE_ATTRIBUTE_PARENT)->key;
        if (null === $key || $key !== $node) {
            return;
        }

        $this->nodeOccurrenceList->removeOccurrence($occurrence);
    }
}
