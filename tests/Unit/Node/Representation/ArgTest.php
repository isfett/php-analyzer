<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation;

use Isfett\PhpAnalyzer\Node\Representation\Arg;
use PhpParser\Node;

/**
 * Class ArgTest
 */
class ArgTest extends AbstractNodeRepresentationTest
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
            'simple' => ['$value', $this->createVariableNode('value'), false, false],
            'byRef' => ['&$value', $this->createVariableNode('value'), true, false],
            'unpack' => ['...$items', $this->createVariableNode('items'), false, true],
            'byRef unpack' => ['...&$items', $this->createVariableNode('items'), true, true],
        ];
    }

    /**
     * @param string    $expectedOutput
     * @param Node\Expr $value
     * @param bool      $byRef
     * @param bool      $unpack
     *
     * @return void
     *
     * @dataProvider argProvider
     */
    public function testGetRepresentation(string $expectedOutput, Node\Expr $value, bool $byRef, bool $unpack): void
    {
        $node = new Node\Arg(
            $value,
            $byRef,
            $unpack,
            $this->getNodeAttributes()
        );

        if ($unpack) {
            $this->representation
                ->method('getRepresentationForNode')
                ->willReturn('$items');
        } else {
            $this->representation
                ->method('getRepresentationForNode')
                ->willReturn('$value');
        }

        $representation = new Arg($this->representation, $node);

        $this->assertEquals($expectedOutput, $representation->getRepresentation());
    }
}
