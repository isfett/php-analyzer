<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\Expr\Cast;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class CastTest
 */
class CastTest extends AbstractNodeRepresentationTest
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
            'int' => ['int', Node\Expr\Cast\Int_::class, ],
            'double' => ['double', Node\Expr\Cast\Double::class],
            'bool' => ['bool', Node\Expr\Cast\Bool_::class],
            'array' => ['array', Node\Expr\Cast\Array_::class],
            'object' => ['object', Node\Expr\Cast\Object_::class],
            'string' => ['string', Node\Expr\Cast\String_::class],
            'unset' => ['unset', Node\Expr\Cast\Unset_::class],
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
    public function testGetRepresentation(string $expectedType, string $nodeClassname): void
    {
        $node = new $nodeClassname(
            $this->createVariableNode('variable'),
            $this->getNodeAttributes()
        );

        $this->representation
            ->method('getRepresentationForNode')
            ->willReturn('$variable');

        $representation = new Cast($this->representation, $node);

        $this->assertEquals(sprintf('(%s) $variable', $expectedType), $representation->getRepresentation());
    }
}
