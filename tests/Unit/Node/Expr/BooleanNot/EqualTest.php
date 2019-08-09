<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Expr\BooleanNot;

use Isfett\PhpAnalyzer\Node\Expr\BooleanNot\Equal;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp;

/**
 * Class EqualTest
 */
class EqualTest extends AbstractNodeTestCase
{
    /**
     * @return void
     */
    public function testNegation(): void
    {
        $node = new BinaryOp\Equal(
            new Expr\Variable('a'),
            new Expr\Variable('b')
        );

        $negatedNode = (new Equal())->negate($node);

        $this->assertInstanceOf(BinaryOp\NotEqual::class, $negatedNode);
        $this->assertEquals($node->left, $negatedNode->left);
        $this->assertEquals($node->right, $negatedNode->right);
        $this->assertEquals($node->getAttributes(), $negatedNode->getAttributes());
    }
}
