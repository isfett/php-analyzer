<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\Expr\ArrowFunction;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class ArrayFunctionTest
 */
class ArrowFunctionTest extends AbstractNodeRepresentationTest
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
    public function arrowFunctionProvider(): array
    {
        return [
            'simple' => ['(fn() => 1)', [], null, false, false],
            'returnType' => ['(fn(): int => 1)', [], 'int', false, false],
            'static' => ['(static fn() => 1)', [], null, true, false],
            'byRef' => ['(fn&() => 1)', [], null, false, true],
            'static byRef' => ['(static fn&() => 1)', [], null, true, true],
            'static byRef returnType' => ['(static fn&(): int => 1)', [], 'int', true, true],
            'returnType params' => [
                '(fn(int $number): int => $number)',
                [
                    new Node\Param(
                        $this->createVariableNode('number'),
                        null,
                        $this->createIdentifierNode('int'),
                        false,
                        false,
                        $this->getNodeAttributes()
                    ),
                ],
                'int',
                false,
                false,
            ],
        ];
    }

    /**
     * @param string      $expectedOutput
     * @param array       $params
     * @param string|null $returnType
     * @param bool        $static
     * @param bool        $byRef
     *
     * @return void
     *
     * @dataProvider arrowFunctionProvider
     */
    public function testGetRepresentation(string $expectedOutput, array $params, ?string $returnType, bool $static, bool $byRef): void
    {
        $node = new Node\Expr\ArrowFunction(
            [
                'static' => $static,
                'byRef' => $byRef,
                'params' => $params,
                'returnType' => $returnType,
                'expr' => new Node\Scalar\LNumber(1, $this->getNodeAttributes()),
            ],
            $this->getNodeAttributes()
        );

        if (null === $returnType) {
            $this->nodeRepresentationService
                ->method('representationForNode')
                ->willReturn('1');
        } elseif (count($params)) {
            $this->nodeRepresentationService
                ->method('representationForNode')
                ->willReturn($returnType, '$number');
        } else {
            $this->nodeRepresentationService
                ->method('representationForNode')
                ->willReturn($returnType, '1');
        }

        if (count($params)) {
            $this->nodeRepresentationService
                ->method('representationForArguments')
                ->willReturn(['int $number']);
        }

        $representation = new ArrowFunction($this->nodeRepresentationService, $node);

        $this->assertEquals($expectedOutput, $representation->representation());
    }
}
