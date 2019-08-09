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
    /** @var string */
    public const OPERATOR_SIGN_IDENTICAL = '===';

    /** @var string */
    public const OPERATOR_SIGN_NOT_IDENTICAL = '!==';

    /** @var string */
    public const OPERATOR_SIGN_EQUAL = '==';

    /** @var string */
    public const OPERATOR_SIGN_NOT_EQUAL = '!=';

    /**
     * @return string
     */
    public function representation(): string
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
            $this->representate($node->left),
            $node->getOperatorSigil(),
            $this->representate($node->right)
        );
    }
}
