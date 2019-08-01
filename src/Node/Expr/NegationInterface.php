<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Expr;

use PhpParser\Node\Expr\BinaryOp;

/**
 * Interface NegationInterface
 */
interface NegationInterface
{
    /**
     * @param BinaryOp $binaryOp
     *
     * @return BinaryOp
     */
    public function negate(BinaryOp $binaryOp): BinaryOp;
}
