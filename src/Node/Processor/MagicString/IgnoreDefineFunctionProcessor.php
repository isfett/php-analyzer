<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Processor\MagicString;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\AbstractProcessor;
use PhpParser\Node;

/**
 * Class IgnoreDefineFunctionProcessor
 */
class IgnoreDefineFunctionProcessor extends AbstractProcessor
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

        $parentParentNode = $node->getAttribute('parent')->getAttribute('parent');
        if ($parentParentNode instanceof Node\Expr\FuncCall &&
            $parentParentNode->name instanceof Node\Name &&
            'define' === $parentParentNode->name->getLast()
        ) {
            $this->nodeOccurrenceList->removeOccurrence($occurrence);
        }
    }
}
