<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Expr\BooleanNot;

use Isfett\PhpAnalyzer\Node\Expr\BooleanNot\NotEqual;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp;

/**
 * Class NotEqualTest
 */
class NotEqualTest extends AbstractNodeTestCase
{
    /**
     * @return void
     */
    public function testNegation(): void
    {
        $node = new BinaryOp\NotEqual(
            new Expr\Variable('a'),
            new Expr\Variable('b')
        );

        $negatedNode = (new NotEqual())->negate($node);

        $this->assertInstanceOf(BinaryOp\Equal::class, $negatedNode);
        $this->assertEquals($node->left, $negatedNode->left);
        $this->assertEquals($node->right, $negatedNode->right);
        $this->assertEquals($node->getAttributes(), $negatedNode->getAttributes());
    }
}
