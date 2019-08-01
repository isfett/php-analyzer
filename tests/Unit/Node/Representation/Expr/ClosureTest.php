<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\Expr\Closure;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class ClosureTest
 */
class ClosureTest extends AbstractNodeRepresentationTest
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
    public function closureProvider(): array
    {
        return [
            'simple' => ['function() { /* CLOSURE */ }', [], [], false, false],
            'static' => ['static function() { /* CLOSURE */ }', [], [], true, false],
            'byRef' => ['function&() { /* CLOSURE */ }', [], [], false, true],
            'static byRef' => ['static function&() { /* CLOSURE */ }', [], [], true, true],
        ];
    }

    /**
     * @param string $expectedOutput
     * @param array  $params
     * @param array  $uses
     * @param bool   $static
     * @param bool   $byRef
     *
     * @return void
     *
     * @dataProvider closureProvider
     */
    public function testGetRepresentation(string $expectedOutput, array $params, array $uses, bool $static, bool $byRef): void
    {
        $node = new Node\Expr\Closure(
            [
                'static' => $static,
                'byRef' => $byRef,
                'params' => $params,
                'uses' => $uses,
            ],
            $this->getNodeAttributes()
        );

        $representation = new Closure($this->representation, $node);

        $this->assertEquals($expectedOutput, $representation->getRepresentation());
    }

    /**
     * @return void
     */
    public function testGetRepresentationWithParams(): void
    {
        $node = new Node\Expr\Closure(
            [
                'static' => false,
                'byRef' => false,
                'params' => [
                    $this->createVariableNode('test'),
                ],
                'uses' => [],
            ],
            $this->getNodeAttributes()
        );

        $this->representation
            ->method('getArguments')
            ->willReturn(['$test']);

        $representation = new Closure($this->representation, $node);

        $this->assertEquals('function($test) { /* CLOSURE */ }', $representation->getRepresentation());
    }

    /**
     * @return void
     */
    public function testGetRepresentationWithUses(): void
    {
        $node = new Node\Expr\Closure(
            [
                'static' => false,
                'byRef' => false,
                'params' => [],
                'uses' => [
                    $this->createVariableNode('test'),
                ],
            ],
            $this->getNodeAttributes()
        );

        $this->representation
            ->method('getArguments')
            ->willReturn(['$test'],[]);

        $representation = new Closure($this->representation, $node);

        $this->assertEquals('function() use ($test) { /* CLOSURE */ }', $representation->getRepresentation());
    }

    /**
     * @return void
     */
    public function testGetRepresentationWithParamsAndUses(): void
    {
        $node = new Node\Expr\Closure(
            [
                'static' => false,
                'byRef' => true,
                'params' => [
                    $this->createVariableNode('test2'),
                ],
                'uses' => [
                    $this->createVariableNode('test'),
                ],
            ],
            $this->getNodeAttributes()
        );

        $this->representation
            ->method('getArguments')
            ->willReturn(['$test'],['$test2']);

        $representation = new Closure($this->representation, $node);

        $this->assertEquals('function&($test2) use ($test) { /* CLOSURE */ }', $representation->getRepresentation());
    }
}
