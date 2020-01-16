<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\Expr\ArrayItem;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class ArrayItemTest
 */
class ArrayItemTest extends AbstractNodeRepresentationTest
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
    public function arrayItemProvider(): array
    {
        return [
            'simple' => ['$value', $this->createVariableNode('value'), null, false, false],
            'byRef' => ['&$value', $this->createVariableNode('value'), null, true, false],
            'simple + key' => ["'key' => \$value", $this->createVariableNode('value'), $this->createScalarStringNode('key'), false, false],
            'unpack' => ['...$items', $this->createVariableNode('items'), null, false, true],
        ];
    }

    /**
     * @param string         $expectedOutput
     * @param Node\Expr      $value
     * @param Node\Expr|null $key
     * @param bool           $byRef
     * @param bool           $unpack
     *
     * @return void
     *
     * @dataProvider arrayItemProvider
     */
    public function testGetRepresentation(string $expectedOutput, Node\Expr $value, ?Node\Expr $key, bool $byRef, bool $unpack): void
    {
        $node = new Node\Expr\ArrayItem(
            $value,
            $key,
            $byRef,
            $this->getNodeAttributes(),
            $unpack
        );

        if ($unpack) {
            $this->nodeRepresentationService
                ->method('representationForNode')
                ->willReturn('$items');
        } elseif (null !== $key) {
            $this->nodeRepresentationService
                ->method('representationForNode')
                ->willReturn("'key'", '$value');
        } else {
            $this->nodeRepresentationService
                ->method('representationForNode')
                ->willReturn('$value');
        }

        $representation = new ArrayItem($this->nodeRepresentationService, $node);

        $this->assertSame($expectedOutput, $representation->representation());
    }
}
