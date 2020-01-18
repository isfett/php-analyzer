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
    /** @var string */
    private const FUNCTION_NAME_DEFINE = 'define';

    /**
     * @param Occurrence $occurrence
     *
     * @return void
     */
    public function process(Occurrence $occurrence): void
    {
        /** @var Node\Scalar\String_ $node */
        $node = $occurrence->getNode();

        $parentParentNode = $node->getAttribute(self::NODE_ATTRIBUTE_PARENT)->getAttribute(self::NODE_ATTRIBUTE_PARENT);
        // phpcs:ignore SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
        if ($parentParentNode instanceof Node\Expr\FuncCall &&
            $parentParentNode->name instanceof Node\Name &&
            self::FUNCTION_NAME_DEFINE === $parentParentNode->name->getLast()
        ) {
            $this->nodeOccurrenceList->removeOccurrence($occurrence);
        }
    }
}
