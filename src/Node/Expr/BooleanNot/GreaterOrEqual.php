<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Expr\BooleanNot;

use Isfett\PhpAnalyzer\Node\Expr\NegationInterface;
use PhpParser\Node\Expr\BinaryOp;

/**
 * Class GreaterOrEqual
 */
class GreaterOrEqual implements NegationInterface
{
    /**
     * @param BinaryOp $binaryOp
     *
     * @return BinaryOp
     */
    public function negate(BinaryOp $binaryOp): BinaryOp
    {
        return new BinaryOp\SmallerOrEqual($binaryOp->left, $binaryOp->right, $binaryOp->getAttributes());
    }
}
