<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Processor;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\AbstractProcessor;
use PhpParser\Node;

/**
 * Class RemoveDuplicateBooleanNotProcessor
 */
class RemoveDuplicateBooleanNotProcessor extends AbstractProcessor
{
    /**
     * @param Occurrence $occurrence
     *
     * @return void
     */
    public function process(Occurrence $occurrence): void
    {
        /** @var Node $node */
        $node = $occurrence->getNode();
        if ($node instanceof Node\Expr\BooleanNot && $node->expr instanceof Node\Expr\BooleanNot) {
            $occurrence->setNode($node->expr->expr);
            $this->markOccurrenceAsAffected($occurrence);
        }
    }
}
