<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Expr\BooleanNot;

use Isfett\PhpAnalyzer\Node\Expr\BooleanNot\GreaterOrEqual;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp;

/**
 * Class GreaterOrEqualTest
 */
class GreaterOrEqualTest extends AbstractNodeTestCase
{
    /**
     * @return void
     */
    public function testNegation(): void
    {
        $node = new BinaryOp\GreaterOrEqual(
            new Expr\Variable('a'),
            new Expr\Variable('b')
        );

        $negatedNode = (new GreaterOrEqual())->negate($node);

        $this->assertInstanceOf(BinaryOp\SmallerOrEqual::class, $negatedNode);
        $this->assertSame($node->left, $negatedNode->left);
        $this->assertSame($node->right, $negatedNode->right);
        $this->assertSame($node->getAttributes(), $negatedNode->getAttributes());
    }
}
