<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\Expr\Clone_;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class Clone_Test
 */
class Clone_Test extends AbstractNodeRepresentationTest
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
        $node = new Node\Expr\Clone_(
            $this->createVariableNode('x'),
            $this->getNodeAttributes()
        );

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('$x');

        $representation = new Clone_($this->nodeRepresentationService, $node);

        $this->assertSame('clone $x', $representation->representation());
    }
}
