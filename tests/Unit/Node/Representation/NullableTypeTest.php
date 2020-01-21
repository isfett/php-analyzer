<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation;

use Isfett\PhpAnalyzer\Node\Representation\NullableType;
use PhpParser\Node;

/**
 * Class NullableTypeTest
 */
class NullableTypeTest extends AbstractNodeRepresentationTest
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @return void
     */
    public function testGetRepresentation(): void
    {
        $node = new Node\NullableType(
            new Node\Param(
                new Node\Expr\Variable('x'),
                null,
                new Node\Identifier('int'),
                false,
                false,
                $this->getNodeAttributes()
            ),
            $this->getNodeAttributes()
        );

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('int');

        $representation = new NullableType($this->nodeRepresentationService, $node);

        $this->assertSame('?int', $representation->representation());
    }
}
