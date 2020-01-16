<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation;

use Isfett\PhpAnalyzer\Node\Representation\Param;
use PhpParser\Node;

/**
 * Class ParamTest
 */
class ParamTest extends AbstractNodeRepresentationTest
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
    public function argProvider(): array
    {
        return [
            'simple' => ['$value', $this->createVariableNode('value'), null, null, false, false],
            'byRef' => ['&$value', $this->createVariableNode('value'), null, null, true, false],
            'returnType byRef' => ['int &$value', $this->createVariableNode('value'), $this->createIdentifierNode('int'), null, true, false],
            'returnType' => ['int $value', $this->createVariableNode('value'), $this->createIdentifierNode('int'), null, false, false],
            'default' => ['$value = $x', $this->createVariableNode('value'), null, $this->createVariableNode('x'), false, false],
            'default returnType' => ['int $value = $x', $this->createVariableNode('value'), $this->createIdentifierNode('int'), $this->createVariableNode('x'), false, false],
            'variadic' => ['...$value', $this->createVariableNode('value'), null, null, false, true],
            'variadic returnType' => ['int ...$value', $this->createVariableNode('value'), $this->createIdentifierNode('int'), null, false, true],
        ];
    }

    /**
     * @param string               $expectedOutput
     * @param Node\Expr\Variable   $value
     * @param Node\Identifier|null $type
     * @param Node\Expr|null       $default
     * @param bool                 $byRef
     * @param bool                 $variadic
     *
     * @return void
     *
     * @dataProvider argProvider
     */
    public function testGetRepresentation(string $expectedOutput, Node\Expr\Variable $value, ?Node\Identifier $type, ?Node\Expr $default, bool $byRef, bool $variadic): void
    {
        $node = new Node\Param(
            $value,
            $default,
            $type,
            $byRef,
            $variadic,
            $this->getNodeAttributes()
        );

        if (null !== $default) {
            $this->nodeRepresentationService
                ->method('representationForNode')
                ->willReturn('$x', '$value');
        } else {
            $this->nodeRepresentationService
                ->method('representationForNode')
                ->willReturn('$value');
        }

        $representation = new Param($this->nodeRepresentationService, $node);

        $this->assertSame($expectedOutput, $representation->representation());
    }
}
