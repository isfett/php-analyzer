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
    private const BOOLEAN_OR_FUNCTION_GET_OPERATOR_SIGIL = 'getOperatorSigil';

    /** @var string */
    private const FORMAT_PARENTHESES = '(%s)';

    /** @var string */
    private const FORMAT_REPRESENTATION = '%s %s %s';

    /** @var string */
    public const OPERATOR_SIGN_EQUAL = '==';

    /** @var string */
    public const OPERATOR_SIGN_IDENTICAL = '===';

    /** @var string */
    public const OPERATOR_SIGN_NOT_EQUAL = '!=';

    /** @var string */
    public const OPERATOR_SIGN_NOT_IDENTICAL = '!==';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\BinaryOp $node */
        $node = $this->node;

        $format = self::FORMAT_REPRESENTATION;

        if (BooleanOr::class === get_class($node) &&
            (
                is_callable([$node->left, self::BOOLEAN_OR_FUNCTION_GET_OPERATOR_SIGIL]) ||
                is_callable([$node->right, self::BOOLEAN_OR_FUNCTION_GET_OPERATOR_SIGIL])
            )
        ) {
            $format = sprintf(self::FORMAT_PARENTHESES, $format);
        }

        return sprintf(
            $format,
            $this->representate($node->left),
            $node->getOperatorSigil(),
            $this->representate($node->right)
        );
    }
}
