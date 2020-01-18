<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Processor\MagicNumber;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\AbstractProcessor;
use PhpParser\Node;

/**
 * Class IgnoreForLoopProcessor
 */
class IgnoreForLoopProcessor extends AbstractProcessor
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

        if (!$node->getAttribute(self::NODE_ATTRIBUTE_PARENT)
                ->getAttribute(self::NODE_ATTRIBUTE_PARENT) instanceof Node\Stmt\For_) {
            return;
        }

        $this->nodeOccurrenceList->removeOccurrence($occurrence);
    }
}
