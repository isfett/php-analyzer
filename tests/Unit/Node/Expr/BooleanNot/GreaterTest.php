<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Expr\BooleanNot;

use Isfett\PhpAnalyzer\Node\Expr\BooleanNot\Greater;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp;

/**
 * Class GreaterTest
 */
class GreaterTest extends AbstractNodeTestCase
{
    /**
     * @return void
     */
    public function testNegation(): void
    {
        $node = new BinaryOp\Greater(
            new Expr\Variable('a'),
            new Expr\Variable('b')
        );

        $negatedNode = (new Greater())->negate($node);

        $this->assertInstanceOf(BinaryOp\Smaller::class, $negatedNode);
        $this->assertSame($node->left, $negatedNode->left);
        $this->assertSame($node->right, $negatedNode->right);
        $this->assertSame($node->getAttributes(), $negatedNode->getAttributes());
    }
}
