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
            $this->nodeRepresentationService
                ->method('representationForNode')
                ->willReturn('$items');
        } else {
            $this->nodeRepresentationService
                ->method('representationForNode')
                ->willReturn('$value');
        }

        $representation = new Arg($this->nodeRepresentationService, $node);

        $this->assertEquals($expectedOutput, $representation->representation());
    }
}
