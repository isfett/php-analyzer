<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class ArrowFunction
 */
class ArrowFunction extends AbstractRepresentation
{
    /** @var string */
    private const OPERATOR = '=>';

    /** @var string */
    private const FORMAT_REPRESENTATION = '(%sfn%s(%s)%s %s %s)';

    /** @var string */
    private const FORMAT_STATIC = 'static ';

    /** @var string */
    private const FORMAT_RETURN_TYPE = ': %s';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\ArrowFunction $node */
        $node = $this->node;

        $static = self::EMPTY_STRING;
        if ($node->static) {
            $static = self::FORMAT_STATIC;
        }

        $returnType = self::EMPTY_STRING;
        if (null !== $node->returnType) {
            $returnType = sprintf(self::FORMAT_RETURN_TYPE, $this->representate($node->returnType));
        }

        return sprintf(
            self::FORMAT_REPRESENTATION,
            $static,
            $node->byRef ? self::REF_SIGN : self::EMPTY_STRING,
            $this->arguments($node->params),
            $returnType,
            self::OPERATOR,
            $this->representate($node->expr)
        );
    }
}
