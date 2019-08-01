<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Expr\Cast;

use Isfett\PhpAnalyzer\Node\Representation\Expr\Cast\Double;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class DoubleTest
 */
class DoubleTest extends AbstractNodeRepresentationTest
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
    public function castProvider(): array
    {
        return [
            'double' => ['double', Node\Expr\Cast\Double::KIND_DOUBLE],
            'float' => ['float', Node\Expr\Cast\Double::KIND_FLOAT],
            'real' => ['real', Node\Expr\Cast\Double::KIND_REAL],
        ];
    }

    /**
     * @param string $expectedType
     * @param string $nodeClassname
     *
     * @return void
     *
     * @dataProvider castProvider
     */
    public function testGetRepresentation(string $expectedType, int $doubleType): void
    {
        $node = new Node\Expr\Cast\Double(
            $this->createVariableNode('variable'),
            array_merge(
                [
                    'kind' => $doubleType,
                ],
                $this->getNodeAttributes()
            )
        );

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('$variable');

        $representation = new Double($this->nodeRepresentationService, $node);

        $this->assertEquals(sprintf('(%s) $variable', $expectedType), $representation->representation());
    }
}
