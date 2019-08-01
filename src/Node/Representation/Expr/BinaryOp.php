<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;

/**
 * Class BinaryOp
 */
class BinaryOp extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function getRepresentation(): string
    {
        /** @var \PhpParser\Node\Expr\BinaryOp $node */
        $node = $this->node;

        $format = '%s %s %s';

        if (BooleanOr::class === get_class($node) &&
            (is_callable([$node->left, 'getOperatorSigil']) ||
            is_callable([$node->right, 'getOperatorSigil']))
        ) {
            $format = sprintf('(%s)', $format);
        }

        return sprintf(
            $format,
            $this->representation($node->left),
            $node->getOperatorSigil(),
            $this->representation($node->right)
        );
    }
}
