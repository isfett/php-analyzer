<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Expr\BooleanNot\Identical;
use Isfett\PhpAnalyzer\Node\Representation\Expr\BinaryOp;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class BinaryOpTest
 */
class BinaryOpTest extends AbstractNodeRepresentationTest
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @return array
     */
    public function binaryOpProvider(): array
    {
        return [
            'bitwiseAnd' => ['$variable & $variable2', Node\Expr\BinaryOp\BitwiseAnd::class],
            'bitwiseOr' => ['$variable | $variable2', Node\Expr\BinaryOp\BitwiseOr::class],
            'bitwiseXor' => ['$variable ^ $variable2', Node\Expr\BinaryOp\BitwiseXor::class],
            'booleanAnd' => ['$variable && $variable2', Node\Expr\BinaryOp\BooleanAnd::class],
            'booleanOr' => ['$variable || $variable2', Node\Expr\BinaryOp\BooleanOr::class],
            'coalesce' => ['$variable ?? $variable2', Node\Expr\BinaryOp\Coalesce::class],
            'div' => ['$variable / $variable2', Node\Expr\BinaryOp\Div::class],
            'equal' => ['$variable == $variable2', Node\Expr\BinaryOp\Equal::class],
            'greater' => ['$variable > $variable2', Node\Expr\BinaryOp\Greater::class],
            'greaterOrEqual' => ['$variable >= $variable2', Node\Expr\BinaryOp\GreaterOrEqual::class],
            'identical' => ['$variable === $variable2', Node\Expr\BinaryOp\Identical::class],
            'logicalAnd' => ['$variable and $variable2', Node\Expr\BinaryOp\LogicalAnd::class],
            'logicalOr' => ['$variable or $variable2', Node\Expr\BinaryOp\LogicalOr::class],
            'logicalXor' => ['$variable xor $variable2', Node\Expr\BinaryOp\LogicalXor::class],
            'minus' => ['$variable - $variable2', Node\Expr\BinaryOp\Minus::class],
            'plus' => ['$variable + $variable2', Node\Expr\BinaryOp\Plus::class],
            'pow' => ['$variable ** $variable2', Node\Expr\BinaryOp\Pow::class],
            'shiftLeft' => ['$variable << $variable2', Node\Expr\BinaryOp\ShiftLeft::class],
            'shiftRight' => ['$variable >> $variable2', Node\Expr\BinaryOp\ShiftRight::class],
            'smaller' => ['$variable < $variable2', Node\Expr\BinaryOp\Smaller::class],
            'smallerOrEqual' => ['$variable <= $variable2', Node\Expr\BinaryOp\SmallerOrEqual::class],
            'spaceship' => ['$variable <=> $variable2', Node\Expr\BinaryOp\Spaceship::class],
        ];
    }

    /**
     * @param string $expectedOutput
     * @param string $nodeClassname
     *
     * @return void
     *
     * @dataProvider binaryOpProvider
     */
    public function testGetRepresentation(string $expectedOutput, string $nodeClassname): void
    {
        $node = new $nodeClassname(
            $this->createVariableNode('variable'),
            $this->createVariableNode('variable2'),
            $this->getNodeAttributes()
        );

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('$variable', '$variable2');

        $representation = new BinaryOp($this->nodeRepresentationService, $node);

        $this->assertEquals($expectedOutput, $representation->representation());
    }

    /**
     * @return void
     */
    public function testGetRepresentationForEncapsuledBooleanOr(): void
    {
        $node = new Node\Expr\BinaryOp\BooleanOr(
            new Node\Expr\BinaryOp\Identical(
                $this->createVariableNode('variable'),
                $this->createVariableNode('variable2'),
                $this->getNodeAttributes()
            ),
            new Node\Expr\BinaryOp\Identical(
                $this->createVariableNode('variable3'),
                $this->createVariableNode('variable4'),
                $this->getNodeAttributes()
            ),
            $this->getNodeAttributes()
        );
        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('$variable === $variable2',  '$variable3 === $variable4');

        $representation = new BinaryOp($this->nodeRepresentationService, $node);

        $this->assertEquals('($variable === $variable2 || $variable3 === $variable4)', $representation->representation());
    }
}
